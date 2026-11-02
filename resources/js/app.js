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

// Replaces native selects in the job filters with a styled list that fits the field width.
// The original select stays in the form, hidden, so submission is unchanged.
document.querySelectorAll('.jobs-search select, .jobs-filter select').forEach((select, index) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'pick';

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'pick__button';
    button.setAttribute('aria-haspopup', 'listbox');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-label', select.getAttribute('aria-label') ?? select.closest('label')?.firstChild?.textContent.trim() ?? '');

    const list = document.createElement('ul');
    list.className = 'pick__list';
    list.id = `pick-${index}`;
    list.setAttribute('role', 'listbox');
    list.hidden = true;
    button.setAttribute('aria-controls', list.id);

    const options = [...select.options].map((option, i) => {
        const item = document.createElement('li');
        item.setAttribute('role', 'option');
        item.textContent = option.textContent;
        item.dataset.index = i;
        list.append(item);
        return item;
    });

    const sync = () => {
        button.textContent = select.options[select.selectedIndex].textContent;
        options.forEach((item, i) => item.setAttribute('aria-selected', String(i === select.selectedIndex)));
    };

    let active = select.selectedIndex;
    const highlight = (i) => {
        active = Math.max(0, Math.min(options.length - 1, i));
        options.forEach((item, n) => item.classList.toggle('is-active', n === active));
        options[active].scrollIntoView({ block: 'nearest' });
    };

    const open = (isOpen) => {
        list.hidden = !isOpen;
        button.setAttribute('aria-expanded', String(isOpen));
        if (isOpen) {
            highlight(select.selectedIndex);
        }
    };

    const choose = (i) => {
        select.selectedIndex = i;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        sync();
        open(false);
        button.focus();
    };

    button.addEventListener('click', () => open(list.hidden));
    list.addEventListener('click', (event) => {
        const item = event.target.closest('li');
        if (item) {
            choose(Number(item.dataset.index));
        }
    });
    button.addEventListener('keydown', (event) => {
        if (list.hidden && ['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
            event.preventDefault();
            open(true);
        } else if (!list.hidden) {
            if (event.key === 'ArrowDown') { event.preventDefault(); highlight(active + 1); }
            if (event.key === 'ArrowUp') { event.preventDefault(); highlight(active - 1); }
            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); choose(active); }
            if (event.key === 'Escape' || event.key === 'Tab') { open(false); }
        }
    });
    document.addEventListener('click', (event) => {
        if (!wrapper.contains(event.target)) {
            open(false);
        }
    });

    select.classList.add('sr-only');
    select.tabIndex = -1;
    select.setAttribute('aria-hidden', 'true');
    select.after(wrapper);
    wrapper.append(button, list);
    sync();
});
