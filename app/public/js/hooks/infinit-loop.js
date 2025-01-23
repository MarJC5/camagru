// Debug configuration
const DEBUG = false;

const debug = (...args) => {
  if (DEBUG) console.log('[InfiniteScroll Debug]:', ...args);
};

// Debug state tracker
let debugState = {
  fetchCount: 0,
  lastFetchTime: null,
  postsLoaded: 0
};

// Utility functions
const sanitizeInput = (str) => {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
};

const validateUrl = (url) => {
  const safeUrl = encodeURI(url);
  const urlPattern = /^https?:\/\/[a-zA-Z0-9-._~:/?#[\]@!$&'()*+,;=]+$/;
  return urlPattern.test(safeUrl) ? safeUrl : '';
};

let currentPage = 0;
let lastFetchedPage = null;
let isAllPostsFetched = false;

document.addEventListener("DOMContentLoaded", () => {
  loadMorePosts();
  setupScrollListener();
});

const loadMorePosts = async () => {
  if (isAllPostsFetched || lastFetchedPage === currentPage + 1) {
    debug('Skipping fetch - All posts fetched or page already loaded');
    return;
  }

  debugState.fetchCount++;
  debugState.lastFetchTime = new Date().toISOString();
  debug('Fetch attempt:', {
    fetchCount: debugState.fetchCount,
    currentPage,
    nextPage: currentPage + 1,
    lastFetchTime: debugState.lastFetchTime
  });

  const nextPage = currentPage + 1;
  const currentPathname = window.location.pathname;
  const params = [`page=${nextPage}`];
  const postsContainer = document.querySelector("#infinit-posts-scroll");

  if (!postsContainer) return;

  if (currentPathname.includes("/user/") || currentPathname.includes("/profile")) {
    const userId = postsContainer.dataset.userId;
    if (userId) params.push(`user_id=${sanitizeInput(userId)}`);
  }

  try {
    const response = await fetch(`/api/posts?${params.join("&")}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const posts = await response.json();

    if (posts.length > 0) {
      currentPage = nextPage;
      lastFetchedPage = nextPage;
      debugState.postsLoaded += posts.length;
      debug('Posts loaded:', {
        newPosts: posts.length,
        totalPosts: debugState.postsLoaded,
        currentPage
      });
      posts.forEach(post => createPostElement(sanitizePost(post)));
      addEventListenersToShareButtons();
    } else if (posts.message === "No more posts") {
      isAllPostsFetched = true;
    }
  } catch (error) {
    console.error("Error fetching posts:", error);
  }
};

const sanitizePost = (post) => {
  return {
    id: sanitizeInput(post.id),
    user: {
      id: sanitizeInput(post.user.id),
      username: sanitizeInput(post.user.username)
    },
    media: {
      src: validateUrl(post.media.src),
      alt: sanitizeInput(post.media.alt)
    },
    current_user: {
      id: sanitizeInput(post.current_user.id),
      has_liked: Boolean(post.current_user.has_liked),
      like: sanitizeFormData(post.current_user.like),
      unlike: sanitizeFormData(post.current_user.unlike)
    },
    count_likes: parseInt(post.count_likes) || 0,
    currentUserLike: post.current_user.has_liked ? 
      sanitizeFormData(post.current_user.unlike) : 
      sanitizeFormData(post.current_user.like)
  };
};

const sanitizeFormData = (data) => {
  if (!data) return {};
  return {
    path: sanitizeInput(data.path),
    csrf: sanitizeInput(data.csrf),
    csrf_name: sanitizeInput(data.csrf_name)
  };
};

const addEventListenersToShareButtons = () => {
  document.querySelectorAll(".run-share-actions").forEach(button => {
    button.removeEventListener("click", handleShareButtonClick);
    button.addEventListener("click", handleShareButtonClick);
  });
};

const handleShareButtonClick = (event) => {
  const target = event.target.closest('.run-share-actions');
  if (!target) return;

  const shareActionId = sanitizeInput(target.dataset.shareActionsId);
  if (!shareActionId) return;

  const shareAction = document.querySelector(`.share-actions-id-${shareActionId}`);
  if (!shareAction) return;

  document.querySelectorAll(".share-actions").forEach(action => {
    if (action !== shareAction) {
      action.classList.add("hidden");
      action.classList.remove("flex");
    }
  });

  shareAction.classList.toggle("hidden");
  shareAction.classList.toggle("flex");

  const clickOutsideHandler = (e) => {
    if (!shareAction.contains(e.target) && !target.contains(e.target)) {
      shareAction.classList.add("hidden");
      shareAction.classList.remove("flex");
      document.removeEventListener("click", clickOutsideHandler);
    }
  };

  document.addEventListener("click", clickOutsideHandler);
  setupShareActionButtons(shareAction, shareActionId);
};

const setupShareActionButtons = (shareAction, shareActionId) => {
  if (!shareAction || !shareActionId) {
    debug('Invalid share action setup attempt');
    return;
  }

  const baseUrl = window.location.origin;
  const postUrl = encodeURIComponent(`${baseUrl}/post/${sanitizeInput(shareActionId)}`);

  const shareUrls = {
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${postUrl}`,
    linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${postUrl}`,
    x: `https://twitter.com/intent/tweet?text=${postUrl}`
  };

  Object.entries(shareUrls).forEach(([platform, url]) => {
    const link = shareAction.querySelector(`.share-${platform}`);
    if (link) link.href = url;
  });
};

const createPostElement = (post) => {
  const postElement = document.createElement("article");
  postElement.classList.add(
    "card", "card--post", "flex", "flex-column", 
    "shadow", "rounded-md", "h-full"
  );
  
  postElement.innerHTML = generatePostHTML(post);
  
  const postsContainer = document.querySelector("#infinit-posts-scroll");
  if (postsContainer) {
    postsContainer.appendChild(postElement);
  }
};

const generatePostHTML = (post) => {
  const currentPathname = window.location.pathname;
  const currentUser = currentPathname.includes("/user/") || currentPathname.includes("/profile");

  return `
    <a href="/user/${post.user.id}" class="px-4 py-3 underline-none ${currentUser ? "hidden" : ""}">
      <div class="card__head flex item-center gap-2">
        <div class="avatar-text flex justify-center item-center bg-gray-100 rounded-full w-8 h-8">
          <p class="capitalize text-bold text-md text-gray-500 m-0">${post.user.username.slice(0, 1)}</p>
        </div>
        <p class="m-0 text-gray-500">@${post.user.username}</p>
      </div>
    </a>
    ${currentUser ? "" : "<hr/>"}
    <div class="card__body h-80 relative ${currentUser ? "rounded-top-md" : ""}">
      <a href="/post/${post.id}" class="absolute top-0 left-0 w-full h-full">
        <figure class="m-0">
          <picture>
            <img class="ofi-image ${currentUser ? "rounded-top-md" : ""}" 
                 src="${post.media.src}" 
                 alt="${post.media.alt}">
          </picture>
        </figure>
      </a>
    </div>
    <hr />
    <div class="card__footer">
      ${generateLikeButton(post)}
    </div>
  `;
};

const generateLikeButton = (post) => {
  const likedClass = post.current_user.has_liked ? "text-red-400" : "text-gray-400";
  const likeAction = generateLikeAction(post);

  return `
    <div class="flex item-center px-4 py-3 gap-2 relative">
      ${likeAction}
      <a href="/post/${post.id}" class="button button--svg">
        <svg class="text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
        </svg>
      </a>
      ${generateShareButtons(post)}
    </div>
    <div class="px-4 pb-3">
      <p class="m-0 ml-1-5 text-gray-500 text-sm">
        ${post.count_likes} ${post.count_likes > 1 ? "Likes" : "Like"}
      </p>
    </div>
  `;
};

const generateLikeAction = (post) => {
  const likedClass = post.current_user.has_liked ? "text-red-400" : "text-gray-400";
  const actionPath = post.currentUserLike.path;

  if (!post.currentUserLike.csrf) {
    return `
      <a href="/${actionPath}">
        <button class="button button--svg">
          <svg class="${likedClass}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
          </svg>
        </button>
      </a>
    `;
  }

  return `
    <form action="/${actionPath}" method="POST">
      <input type="hidden" name="${post.currentUserLike.csrf_name}" value="${post.currentUserLike.csrf}">
      <input type="hidden" name="post_id" value="${post.id}">
      <input type="hidden" name="user_id" value="${post.current_user.id}">
      <input type="hidden" name="id" value="${post.current_user.has_liked}">
      <button class="button button--svg" type="submit">
        <svg class="${likedClass}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
      </button>
    </form>
  `;
};

const generateShareButtons = (post) => {
  return `
    <button class="button button--svg run-share-actions" data-share-actions-id="${post.id}">
      <svg class="text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
      </svg>
    </button>
    <div class="share-actions share-actions-id-${post.id} absolute item-center p-2 gap-2 bg-white rounded-lg hidden">
      <p class="m-0 text-sm text-gray-500">Share</p>
      <div class="actions flex gap-2 item-center justify-between">
        <a href="#" class="button-share share share-x" target="_blank" rel="noopener noreferrer">
          <svg stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.9 1.2h3.7l-8 9.1L24 22.8h-7.4l-5.8-7.5-6.6 7.5H.5L9 13 0 1.2h7.6L12.8 8Zm-1.3 19.4h2L6.5 3.2H4.3Z"/>
          </svg>
        </a>
        <a href="#" class="button-share share share-facebook" target="_blank" rel="noopener noreferrer">
          <svg stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.1 23.7v-8H6.6V12h2.5v-1.5c0-4.1 1.8-6 5.9-6h1.4a8.7 8.7 0 0 1 1.2.3V8a8.6 8.6 0 0 0-.7 0 26.8 26.8 0 0 0-.7 0c-.7 0-1.3 0-1.7.3a1.7 1.7 0 0 0-.7.6c-.2.4-.3 1-.3 1.7V12h3.9l-.4 2.1-.3 1.6h-3.2V24a12 12 0 1 0-4.4-.3Z"/>
          </svg>
        </a>
        <a href="#" class="button-share share share-linkedin" target="_blank" rel="noopener noreferrer">
          <svg stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.4 20.5H17v-5.6c0-1.3 0-3-1.9-3-1.8 0-2 1.4-2 2.9v5.7H9.3V9h3.4v1.6c.5-1 1.6-1.9 3.4-1.9 3.6 0 4.2 2.4 4.2 5.5v6.3zm-15-13a2 2 0 1 1 0-4.2 2 2 0 0 1 0 4.1zm1.7 13H3.6V9H7v11.5zM22.2 0H1.8C.8 0 0 .8 0 1.7v20.6c0 1 .8 1.7 1.8 1.7h20.4c1 0 1.8-.8 1.8-1.7V1.7c0-1-.8-1.7-1.8-1.7z"/>
          </svg>
        </a>
      </div>
    </div>
  `;
};

const setupScrollListener = () => {
  let scrollTimeout;
  
  const handleScroll = () => {
    if (scrollTimeout) clearTimeout(scrollTimeout);
    
    scrollTimeout = setTimeout(() => {
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const scrollHeight = document.documentElement.scrollHeight;
      const clientHeight = window.innerHeight || document.documentElement.clientHeight;
      
      if (scrollTop + clientHeight >= scrollHeight - 5) {
        loadMorePosts();
      }
    }, 100);
  };

  window.addEventListener("scroll", handleScroll, { passive: true });
};