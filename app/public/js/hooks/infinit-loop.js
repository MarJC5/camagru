import { escapeHTML } from "./utils.js";

document.addEventListener("DOMContentLoaded", () => {
  console.log("Ready to look for infinite posts loop");

  let currentPage = 0;
  const postsContainer = document.querySelector("#infinit-posts-scroll");

  const loadMorePosts = async () => {
    currentPage++;
    const currentPathname = window.location.pathname;
    const params = [];
    params.push(`page=${currentPage}`);
    if (currentPathname.includes("/user/")) {
      const userId = currentPathname.split("/")[2];
      params.push(`user_id=${userId}`);
    }

    try {
      const response = await fetch(`/api/posts?${params.join("&")}`);

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const posts = await response.json();
      console.log(posts);

      if (posts.length > 0) {
        posts.forEach((post) => {
          const postElement = document.createElement("article");
          postElement.classList.add(
            "card",
            "card--post",
            "flex",
            "flex-column",
            "shadow",
            "rounded-md",
            "h-full"
          );

          postElement.innerHTML = `
                        <a href="/user/${escapeHTML(
                          post.user.id
                        )}" class="px-4 py-3 underline-none ${
            currentPathname.includes("/user/") ? "hidden" : ""
          }">
                            <div class="card__head flex item-center gap-2">
                                <div class="avatar-text flex justify-center item-center bg-gray-100 rounded-full w-8 h-8">
                                    <p class="capitalize text-bold text-md text-gray-500 m-0">
                                        ${escapeHTML(
                                          post.user.username.slice(0, 1)
                                        )}
                                    </p>
                                </div>
                                <p class="m-0 text-gray-500">@${escapeHTML(
                                  post.user.username
                                )}</p>
                            </div>
                        </a>
                        <hr ${
                          currentPathname.includes("/user/") ? "hidden" : ""
                        }/>
                        <div class="card__body h-80 relative ${
                          currentPathname.includes("/user/")
                            ? "rounded-top-md"
                            : ""
                        }">
                        <a href="/post/${escapeHTML(post.id)}" class="absolute top-0 left-0 w-full h-full">
                            <figure class="m-0">
                                <picture>
                                    <img
                                    class="ofi-image ${
                                      currentPathname.includes("/user/")
                                        ? "rounded-top-md"
                                        : ""
                                    }"
                                    srcset="${escapeHTML(post.media.src)}"
                                    src="${escapeHTML(post.media.src)}"
                                    alt="${escapeHTML(post.media.alt)}">
                                </picture>
                            </figure>
                            </a>
                        </div>
                        <hr />
                        <div class="card__footer">
                            <div class="flex item-center px-4 py-3 gap-2 relative">
                                <button class="button button--svg">
                                    <svg class="text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                    </svg>
                                </button>
                                <button class="button button--svg">
                                    <svg class="text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                    </svg>
                                </button>
                                <button class="button button--svg run-share-actions" data-share-actions-id="${escapeHTML(
                                  post.id
                                )}">
                                    <svg class="text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                    </svg>
                                </button>
                                <div class="share-actions share-actions-id-${escapeHTML(
                                  post.id
                                )} absolute item-center p-2 gap-2 bg-white rounded-lg hidden">
                                    <p class="m-0 text-sm text-gray-500">Share</p>
                                    <div class="actions flex gap-2 item-center justify-between">
                                        <button class="button-share share share-x">
                                            <svg stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.9 1.2h3.7l-8 9.1L24 22.8h-7.4l-5.8-7.5-6.6 7.5H.5L9 13 0 1.2h7.6L12.8 8Zm-1.3 19.4h2L6.5 3.2H4.3Z"/></svg>
                                            </button>
                                        <button class="button-share share share-facebook">
                                            <svg stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9.1 23.7v-8H6.6V12h2.5v-1.5c0-4.1 1.8-6 5.9-6h1.4a8.7 8.7 0 0 1 1.2.3V8a8.6 8.6 0 0 0-.7 0 26.8 26.8 0 0 0-.7 0c-.7 0-1.3 0-1.7.3a1.7 1.7 0 0 0-.7.6c-.2.4-.3 1-.3 1.7V12h3.9l-.4 2.1-.3 1.6h-3.2V24a12 12 0 1 0-4.4-.3Z"/></svg>
                                            </button>
                                        <button class="button-share share share-linkedin">
                                            <svg stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.4 20.5H17v-5.6c0-1.3 0-3-1.9-3-1.8 0-2 1.4-2 2.9v5.7H9.3V9h3.4v1.6c.5-1 1.6-1.9 3.4-1.9 3.6 0 4.2 2.4 4.2 5.5v6.3zm-15-13a2 2 0 1 1 0-4.2 2 2 0 0 1 0 4.1zm1.7 13H3.6V9H7v11.5zM22.2 0H1.8C.8 0 0 .8 0 1.7v20.6c0 1 .8 1.7 1.8 1.7h20.4c1 0 1.8-.8 1.8-1.7V1.7c0-1-.8-1.7-1.8-1.7z"/></svg>
                                        </button>
                                    </div>
                                <div>
                            </div>
                        </div>
                    `;
          postsContainer.appendChild(postElement);
        });

        const shareActions = document.querySelectorAll(".run-share-actions");

        if (shareActions) {
          shareActions.forEach((shareAction) => {
            if (!shareAction) return;

            shareAction.addEventListener("click", (event) => {
                let nodeItem = event.target;
                if (nodeItem.tagName === "svg") {
                    nodeItem = nodeItem.parentNode;
                } else if (nodeItem.tagName === "path") {
                    nodeItem = nodeItem.parentNode.parentNode;
                }
                const shareAction = document.querySelector(
                    `.share-actions-id-${nodeItem.dataset.shareActionsId}`
                );
                const shareActionId = nodeItem.dataset.shareActionsId;

                // close all share actions
                document.querySelectorAll(".share-actions").forEach((shareAction) => {
                    shareAction.classList.add("hidden");
                    shareAction.classList.remove("flex");
                });

                shareAction.classList.remove("hidden");
                shareAction.classList.add("flex");

                // reset click outside
                document.addEventListener("click", (event) => {
                    if (!shareAction.contains(event.target) && !nodeItem.contains(event.target)) {
                        shareAction.classList.add("hidden");
                        shareAction.classList.remove("flex");
                    }
                });

                shareAction
                .querySelector(".share-facebook")
                .addEventListener("click", () => {
                  const postUrl =
                    window.location.origin + `/post/${shareActionId}`;
                  const facebookShareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
                    postUrl
                  )}`;
                  window.open(facebookShareUrl, "_blank");
                });

                shareAction
                .querySelector(".share-linkedin")
                .addEventListener("click", () => {
                  const postUrl =
                    window.location.origin + `/post/${shareActionId}`;
                  const linkedinShareUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(
                    postUrl
                  )}`;
                  window.open(linkedinShareUrl, "_blank");
                });

                shareAction
                .querySelector(".share-x")
                .addEventListener("click", () => {
                  const postUrl =
                    window.location.origin + `/post/${shareActionId}`;
                  const linkedinShareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(
                    postUrl
                  )}`;
                  window.open(linkedinShareUrl, "_blank");
                });
            });
          });
        }
      } else {
        console.log("No more posts to load.");
        window.removeEventListener("scroll", handleScroll);
      }
    } catch (error) {
      console.error("Error fetching posts:", error);
    }
  };

  const handleScroll = () => {
    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight;
    const clientHeight =
      window.innerHeight || document.documentElement.clientHeight;

    if (scrollTop + clientHeight >= scrollHeight - 5) {
      loadMorePosts();
    }
  };

  // Initial load
  loadMorePosts();

  window.addEventListener("scroll", handleScroll);
});
