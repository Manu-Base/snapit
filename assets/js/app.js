// App-wide enhancements for SnapIt.

(() => {
  const timer = document.getElementById('session-timer');
  if (timer) {
    let seconds = 0;
    setInterval(() => {
      seconds += 1;
      const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
      const secs = String(seconds % 60).padStart(2, '0');
      timer.textContent = `Session time: ${mins}:${secs}`;
    }, 1000);
  }

  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js').catch(() => {
      // Progressive enhancement only.
    });
  }
})();
