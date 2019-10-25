export function GifImageBlock (gifUrl) {
    const element = document.createElement('img');
    element.src = gifUrl;
    return element;
}