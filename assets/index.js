document.querySelector('.themetoggle').addEventListener('click', (event) => {
    event.preventDefault()
    if (localStorage.getItem('theme') === 'dark') {
        localStorage.removeItem('theme')
    }
    else {
        localStorage.setItem('theme', 'dark')
    }
    addDarkClassToHTML()
})

function addDarkClassToHTML() {
    try {
        if (localStorage.getItem('theme') === 'dark') {
            document.querySelector('html').classList.add('dark')
            document.querySelector('h4').classList.add('dark')
            document.querySelector('.themetoggle').classList.remove('btn-dark')
            document.querySelector('.themetoggle').classList.add('btn-light')
            document.querySelector('.themetoggle span').textContent = 'dark_mode'
        }
        else {
            document.querySelector('html').classList.remove('dark')
            document.querySelector('h4').classList.remove('dark')
            document.querySelector('.themetoggle').classList.remove('btn-light')
            document.querySelector('.themetoggle').classList.add('btn-dark')
            document.querySelector('.themetoggle span').textContent = 'wb_sunny'
        }
    } catch (err) { }
}

addDarkClassToHTML()