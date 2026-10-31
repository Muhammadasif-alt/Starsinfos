const menu = document.getElementById('m');

document.querySelector('.burger')?.addEventListener('click', (event) => {
    const isOpen = menu.classList.toggle('open');
    event.currentTarget.setAttribute('aria-expanded', String(isOpen));
});

document.querySelectorAll('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
        await navigator.clipboard.writeText(button.dataset.copy);
        button.textContent = 'Copied!';
    });
});

const newsletter = document.getElementById('newsletter');

newsletter?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const status = newsletter.querySelector('.nl-msg');
    const button = newsletter.querySelector('button');
    button.disabled = true;

    try {
        const response = await fetch(newsletter.action, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(newsletter),
        });
        const data = await response.json();

        status.textContent = response.status === 429
            ? 'Too many attempts. Please try again in a minute.'
            : (data.errors?.email?.[0] ?? data.message);

        if (response.ok) {
            newsletter.reset();
        }
    } catch {
        status.textContent = 'Something went wrong. Please try again.';
    } finally {
        button.disabled = false;
    }
});
