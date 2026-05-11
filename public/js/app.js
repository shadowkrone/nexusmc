// NexusMC — Frontend JS

// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-flash]').forEach(el => {
    setTimeout(() => el.classList.add('opacity-0', 'transition-opacity', 'duration-500'), 5000);
    setTimeout(() => el.remove(), 5600);
  });

  // Live server status refresh every 60s
  const statusWidget = document.getElementById('server-status-live');
  if (statusWidget) {
    setInterval(async () => {
      try {
        const res = await fetch('/api/server');
        const data = await res.json();
        const countEl = document.getElementById('player-count');
        if (countEl && data.players_online !== undefined) {
          countEl.textContent = data.players_online;
        }
      } catch {}
    }, 60000);
  }
});
