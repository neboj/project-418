export function VisibilitySwitcher (parentEl, childEl, counterEl) {
    this.parentEl = parentEl;
    this.childEl = childEl;
    this.parentEl.addEventListener('click', () => {
        if (this.childEl.style.display === 'block') {
            this.childEl.style.display = 'none';
        } else {
            this.childEl.style.display = 'block';
            let count = counterEl.innerText;
            if (parseInt(count) === 0) {
                this.childEl.style.display = "none";
            }
        }
    });
    // document.addEventListener('mouseup',(e) => {
    //     // if the target of the click isn't the container nor a descendant of the container
    //     if (!this.childEl == e.target && this.childEl.has(e.target).length === 0) {
    //         this.childEl.css('display','none');
    //     }
    //  TODO click outside closes container(parent)
    // });
}