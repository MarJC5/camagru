document.addEventListener("DOMContentLoaded", () => {
  const controls = document.querySelector(".controls");
  const cameraOptions = document.querySelector(".video-options>select");
  const video = document.querySelector("video");
  const canvas = document.querySelectorAll("canvas");
  const screenshotImage = document.querySelectorAll(".screenshot-image");
  const screenshotsCountDisplay = document.querySelector(".screenshots-count");
  const buttons = [...controls.querySelectorAll("button")];
  let streamStarted = false;
  const MAX_SCREENSHOTS = 4; // Set the maximum number of screenshots

  screenshotsCountDisplay.innerHTML = `0 / ${MAX_SCREENSHOTS}`;

  const [play, pause, screenshot] = buttons;

  const constraints = {
    video: {
      width: {
        min: 1280,
        ideal: 1920,
        max: 2560,
      },
      height: {
        min: 720,
        ideal: 1080,
        max: 1440,
      },
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
    const screenshotDataSet = document.querySelector("#screenshots-container");
    let count = parseInt(screenshotDataSet.getAttribute("data-screenshots"));
    if (count < MAX_SCREENSHOTS) {
      canvas[count].width = video.videoWidth;
      canvas[count].height = video.videoHeight;
      canvas[count].getContext('2d').drawImage(video, 0, 0);
      screenshotImage[count].src = canvas[count].toDataURL('image/webp');
      screenshotImage[count].classList.remove("hidden");
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