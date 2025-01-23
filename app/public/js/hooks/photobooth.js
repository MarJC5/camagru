document.addEventListener("DOMContentLoaded", () => {
  const controls = document.querySelector(".controls");
  const cameraOptions = document.querySelector(".video-options>select");
  const video = document.querySelector("video");
  const screenshotsContainer = document.querySelector(".screenshots");
  const screenshots = document.querySelectorAll(".screenshot");
  const canvas = document.querySelectorAll(".screenshot canvas");
  const screenshotImage = document.querySelectorAll(".screenshot-image");
  const screenshotImageStickers = document.querySelectorAll(
    ".screenshot-image-sticker"
  );
  const mainCanvas = document.getElementById("upload-image-canvas");
  const ctx = mainCanvas.getContext("2d");
  const stickersButton = document.querySelectorAll(".button--sticker");
  const screenshotsCountDisplay = document.querySelector(".screenshots-count");
  const buttons = [...controls.querySelectorAll("button")];
  const uploadButton = document.querySelector(".upload");
  const imageUploadInput = document.getElementById("imageUpload");
  let streamStarted = false;
  let currentSticker = null;
  const MAX_SCREENSHOTS = 4; // Set the maximum number of screenshots

  stickersButton.forEach((stickerButton) => {
    stickerButton.addEventListener("click", () => {
      const sticker = stickerButton.getAttribute("data-sticker");
      const stickerElement = document.querySelector(
        `.sticker[data-sticker="${sticker}"]`
      );

      if (currentSticker) {
        currentSticker.classList.add("hidden");
      }

      if (stickerElement) {
        stickerElement.classList.remove("hidden");
        currentSticker = stickerElement;
        document.querySelector(".camera").classList.add("allowed");
      }

      if (screenshot.classList.contains("button--disabled")) {
        screenshot.classList.remove("button--disabled");
      }
    });
  });

  screenshotsCountDisplay.innerHTML = `0 / ${MAX_SCREENSHOTS}`;

  const [play, pause, screenshot] = buttons;

  const constraints = {
    video: {
      width: {
        ideal: 500,
      },
      height: {
        ideal: 500,
      },
      aspectRatio: 1,
    },
  };

  // Set up main canvas dimensions
  mainCanvas.width = 500;
  mainCanvas.height = 500;

  cameraOptions.onchange = () => {
    const updatedConstraints = {
      ...constraints,
      deviceId: {
        exact: cameraOptions.value,
      },
    };

    startStream(updatedConstraints);
  };

  play.onclick = () => {
    if (streamStarted) {
      video.play();
      play.classList.add("hidden");
      pause.classList.remove("hidden");
      return;
    }
    if ("mediaDevices" in navigator && navigator.mediaDevices.getUserMedia) {
      const updatedConstraints = {
        ...constraints,
        deviceId: {
          exact: cameraOptions.value,
        },
      };
      startStream(updatedConstraints);
    }
  };

  const pauseStream = () => {
    video.pause();
    play.classList.remove("hidden");
    pause.classList.add("hidden");
  };

  const doScreenshot = () => {
    // Check that button doesnt have button--disabled class
    if (screenshot.classList.contains("button--disabled")) {
      return;
    }

    const screenshotDataSet = document.querySelector("#screenshots-container");
    let count = parseInt(screenshotDataSet.getAttribute("data-screenshots"));
    if (count < MAX_SCREENSHOTS) {
      const ctx = canvas[count].getContext("2d");
      const canvasWidth = constraints.video.width.ideal;
      const canvasHeight = constraints.video.height.ideal;
      const videoWidth = video.videoWidth;
      const videoHeight = video.videoHeight;

      canvas[count].width = canvasWidth;
      canvas[count].height = canvasHeight;

      // Calculate the scaling factor to maintain aspect ratio
      const scale = Math.min(
        canvasWidth / videoWidth,
        canvasHeight / videoHeight
      );
      const scaledWidth = videoWidth * scale;
      const scaledHeight = videoHeight * scale;
      const x = (canvasWidth - scaledWidth) / 2;
      const y = (canvasHeight - scaledHeight) / 2;

      // Fill the canvas with white background
      ctx.fillStyle = "white";
      ctx.fillRect(0, 0, canvasWidth, canvasHeight);

      // Draw the video frame centered within the canvas
      ctx.drawImage(
        video,
        0,
        0,
        videoWidth,
        videoHeight,
        x,
        y,
        scaledWidth,
        scaledHeight
      );

      screenshotImage[count].src = canvas[count].toDataURL("image/webp");
      screenshotImage[count].alt = `Screenshot ${count + 1}`;
      screenshots[count].classList.remove("hidden");
      screenshotImage[count].classList.remove("hidden");
      screenshotImageStickers[count].src = currentSticker
        ? currentSticker.src
        : "";
      screenshotImageStickers[count].alt = currentSticker
        ? currentSticker.alt
        : "";
      screenshotImageStickers[count].classList.remove("hidden");
      count++; // Increment the counter
      screenshotDataSet.setAttribute("data-screenshots", count);
      screenshotsCountDisplay.innerHTML = `${count} / ${MAX_SCREENSHOTS}`;
    } else {
      alert(`Maximum of ${MAX_SCREENSHOTS} screenshots reached.`);
    }
  };

  pause.onclick = pauseStream;
  screenshot.onclick = doScreenshot;

  const startStream = async (constraints) => {
    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    handleStream(stream);
  };

  const handleStream = (stream) => {
    video.srcObject = stream;
    play.classList.add("hidden");
    pause.classList.remove("hidden");
    screenshot.classList.remove("hidden");
  };

  const getCameraSelection = async () => {
    if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
      console.log("enumerateDevices() not supported.");
      return;
    }

    try {
      const devices = await navigator.mediaDevices.enumerateDevices();
      const videoDevices = devices.filter(
        (device) => device.kind === "videoinput"
      );
      const options = videoDevices.map((videoDevice) => {
        return `<option value="${videoDevice.deviceId}">${
          videoDevice.label ||
          "Camera " + (videoDevices.indexOf(videoDevice) + 1)
        }</option>`;
      });
      cameraOptions.innerHTML = options.join("");
    } catch (error) {
      console.error("Error accessing media devices.", error);
    }
  };

  if ("mediaDevices" in navigator && navigator.mediaDevices.getUserMedia) {
    console.log("getUserMedia supported.");
    getCameraSelection();
  } else {
    console.error(
      "getUserMedia is not supported by your browser or the site is not loaded in a secure context."
    );
  }

  uploadButton.addEventListener("click", () => {
    imageUploadInput.click(); // Trigger file input click
  });

  // Handle file selection
  imageUploadInput.addEventListener("change", (event) => {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        if (streamStarted) {
          pauseStream();
        }

        // if video is upper, hide it
        if (!video.classList.contains("hidden")) {
          video.classList.add("hidden");
        }

        displayImageOnMainCanvas(e.target.result);
      };
      reader.readAsDataURL(file); // Read the file as base64
    }
  });

  const displayImageOnMainCanvas = (imageSrc) => {
    const img = new Image();
    img.onload = () => {
      const canvasWidth = mainCanvas.width;
      const canvasHeight = mainCanvas.height;
      const imgWidth = img.width;
      const imgHeight = img.height;

      // Calculate the scaling factor to maintain aspect ratio
      const scale = Math.min(canvasWidth / imgWidth, canvasHeight / imgHeight);
      const scaledWidth = imgWidth * scale;
      const scaledHeight = imgHeight * scale;
      const x = (canvasWidth - scaledWidth) / 2;
      const y = (canvasHeight - scaledHeight) / 2;

      // Fill the canvas with white background
      ctx.fillStyle = "white";
      ctx.fillRect(0, 0, canvasWidth, canvasHeight);

      // Draw the image on the canvas
      ctx.drawImage(img, x, y, scaledWidth, scaledHeight);

      // toggle the hidden class on the mainCanvas
      mainCanvas.classList.remove("hidden");
      screenshotsContainer.classList.add("hidden");
      document.querySelector(".video-options").classList.add("hidden");
      document.querySelector(".play").classList.add("hidden");
      document.querySelector(".pause").classList.add("hidden");
      document.querySelector(".screenshot").classList.add("hidden");
      document.querySelector(".camera").classList.add("upload");
    };
    img.src = imageSrc;
  };
});

document.addEventListener("DOMContentLoaded", function () {
  const publishButtons = document.querySelectorAll(".button--send");
  let currentSticker = null;

  publishButtons.forEach((publishButton) => {
    publishButton.addEventListener("click", () => {
      const csrfShot = document.querySelector(
        '[name="csrf_upload_shot_media"]'
      ).value;
      const csrfUpload = document.querySelector(
        '[name="csrf_upload_media"]'
      ).value;
      const stickers = document.querySelectorAll(".sticker");

      // select sticker without hiding class
      stickers.forEach((sticker) => {
        if (!sticker.classList.contains("hidden")) {
          currentSticker = sticker;
        }
      });

      // Create FormData object
      const formData = new FormData();
      if (publishButton.classList.contains("button--send--upload")) {
        const mainCanvas = document.getElementById("upload-image-canvas");
        const mainCanvasData = mainCanvas.toDataURL("image/webp");
        formData.append("sticker", currentSticker.src.split("/").pop());
        formData.append("image", mainCanvasData);
        formData.append("media_mode", "upload");
        formData.append("csrf_upload_media", csrfUpload); // Add CSRF token to FormData
      } else {
        const screenshot = publishButton.closest(".screenshot");
        const screenshotImage = screenshot.querySelector(".screenshot-image");
        const screenshotImageSticker = screenshot.querySelector(
          ".screenshot-image-sticker"
        );
        const stickerFilename = screenshotImageSticker.src.split("/").pop();
        formData.append("image", screenshotImage.src);

        formData.append("sticker", stickerFilename);
        formData.append("media_mode", "shot");
        formData.append("csrf_upload_shot_media", csrfShot); // Add CSRF token to FormData
      }

      // Use fetch to send FormData
      fetch("/media/upload", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (response.ok) {
            return response.json();
          }
          throw new Error("Request failed.");
        })
        .then((data) => {
          console.log(data);
          if (data.status === 200) {
            window.location.href = data.redirect_url;
          } else {
            window.location.href = "/post/create"; // Redirect to create post page
          }
        })
        .catch((error) => {
          console.log(error);
          window.location.href = "/post/create"; // Redirect to create post page
        });
    });
  });
});
