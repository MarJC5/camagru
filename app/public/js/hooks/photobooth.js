document.addEventListener("DOMContentLoaded", () => {
  const controls = document.querySelector(".controls");
  const cameraOptions = document.querySelector(".video-options>select");
  const video = document.querySelector("video");
  const canvas = document.querySelectorAll(".screenshot canvas");
  const screenshotImage = document.querySelectorAll(".screenshot-image");
  const screenshotImageStickers = document.querySelectorAll(".screenshot-image-sticker");
  const stickersButton = document.querySelectorAll(".button--sticker");
  const stickers = document.querySelectorAll(".sticker");
  const screenshotsCountDisplay = document.querySelector(".screenshots-count");
  const buttons = [...controls.querySelectorAll("button")];
  let streamStarted = false;
  let currentSticker = null;
  const MAX_SCREENSHOTS = 4; // Set the maximum number of screenshots

  stickersButton.forEach(stickerButton => {
    stickerButton.addEventListener("click", () => {
      const sticker = stickerButton.getAttribute("data-sticker");
      const stickerElement = document.querySelector(`.sticker[data-sticker="${sticker}"]`);

      if (currentSticker) {
        currentSticker.classList.add("hidden");
      }

      if (stickerElement) {
        stickerElement.classList.remove("hidden");
        currentSticker = stickerElement;
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
      aspectRatio: 1
    },
  };

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
      const ctx = canvas[count].getContext('2d');
      const canvasWidth = constraints.video.width.ideal;
      const canvasHeight = constraints.video.height.ideal;
      const videoWidth = video.videoWidth;
      const videoHeight = video.videoHeight;

      canvas[count].width = canvasWidth;
      canvas[count].height = canvasHeight;

      // Calculate the scaling factor to maintain aspect ratio
      const scale = Math.min(canvasWidth / videoWidth, canvasHeight / videoHeight);
      const scaledWidth = videoWidth * scale;
      const scaledHeight = videoHeight * scale;
      const x = (canvasWidth - scaledWidth) / 2;
      const y = (canvasHeight - scaledHeight) / 2;

      // Fill the canvas with white background
      ctx.fillStyle = "white";
      ctx.fillRect(0, 0, canvasWidth, canvasHeight);

      // Draw the video frame centered within the canvas
      ctx.drawImage(video, 0, 0, videoWidth, videoHeight, x, y, scaledWidth, scaledHeight);

      screenshotImage[count].src = canvas[count].toDataURL('image/webp');
      screenshotImage[count].classList.remove("hidden");
      screenshotImageStickers[count].src = currentSticker ? currentSticker.src : "";
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
      console.log('enumerateDevices() not supported.');
      return;
    }
  
    try {
      const devices = await navigator.mediaDevices.enumerateDevices();
      const videoDevices = devices.filter(device => device.kind === 'videoinput');
      const options = videoDevices.map(videoDevice => {
        return `<option value="${videoDevice.deviceId}">${videoDevice.label || 'Camera ' + (videoDevices.indexOf(videoDevice) + 1)}</option>`;
      });
      cameraOptions.innerHTML = options.join('');
    } catch (error) {
      console.error('Error accessing media devices.', error);
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
});