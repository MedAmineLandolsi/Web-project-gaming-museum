(function () {
  function qs(sel, root) {
    return (root || document).querySelector(sel);
  }

  async function postJson(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });

    const data = await res.json().catch(() => null);
    if (!data || typeof data !== 'object') {
      throw new Error('Invalid AI response');
    }
    if (!data.ok) {
      throw new Error(data.error || 'AI error');
    }
    return data;
  }

  function setSelectValue(selectEl, value) {
    if (!selectEl) return;
    const options = Array.from(selectEl.options || []);
    const match = options.find((o) => (o.value || '').toLowerCase() === String(value || '').toLowerCase());
    if (match) {
      selectEl.value = match.value;
      selectEl.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const box = qs('[data-ai-assist]');
    if (!box) return;

    const endpoint = box.getAttribute('data-ai-endpoint') || 'api/ai-assist.php';
    const lang = box.getAttribute('data-lang') || 'fr';

    const input = qs('#aiAssistInput', box);
    const btn = qs('#aiAssistBtn', box);
    const status = qs('#aiAssistStatus', box);

    const typeEl = qs('#typeReclamation');
    const titreEl = qs('#titre');
    const descEl = qs('#description');

    if (!input || !btn || !status) return;

    btn.addEventListener('click', async () => {
      const text = (input.value || '').trim();
      status.classList.remove('is-error', 'is-success');

      if (text.length < 8) {
        status.textContent = status.getAttribute('data-min') || 'Please add more details.';
        status.classList.add('is-error');
        return;
      }

      btn.disabled = true;
      btn.setAttribute('aria-busy', 'true');
      status.textContent = status.getAttribute('data-loading') || 'Thinking...';

      try {
        const res = await postJson(endpoint, { text, lang });
        const data = (res && res.data) || {};

        if (data.typeReclamation) setSelectValue(typeEl, data.typeReclamation);
        if (data.titre && titreEl) {
          titreEl.value = data.titre;
          titreEl.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (data.description && descEl) {
          descEl.value = data.description;
          descEl.dispatchEvent(new Event('input', { bubbles: true }));
        }

        status.textContent = status.getAttribute('data-done') || 'Suggestions applied.';
        status.classList.add('is-success');
      } catch (e) {
        status.textContent = (e && e.message) ? e.message : (status.getAttribute('data-error') || 'AI error');
        status.classList.add('is-error');
      } finally {
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
      }
    });
  });
})();
