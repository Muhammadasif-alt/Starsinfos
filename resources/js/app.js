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
