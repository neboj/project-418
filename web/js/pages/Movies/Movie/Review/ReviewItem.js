import {GifImageBlock} from "../GifImage/GifImageBlock.js";
import {Profile} from "../../../../components/User/Profile/Profile.js";

export function ReviewItem (isGif, review_id, review_date, reviewInput, gifUrl) {
    const profile = new Profile();
    const reviewBlockEl = document.createElement('li');
    reviewBlockEl.className = 'collection-item avatar mov-rev';
    reviewBlockEl.id = review_id;
    const userImageEl = profile.getImageElement();
    const titleEl = document.createElement('span');
    titleEl.className = 'title';
    titleEl.innerText = profile.getName();
    let paragraphEl = document.createElement('p');
    if (isGif) {
        paragraphEl.appendChild(new GifImageBlock(gifUrl));
    } else {
        paragraphEl.innerText = reviewInput;
    }
    const dateEl = document.createElement('span');
    dateEl.className = 'secondary-content rev-time';
    dateEl.innerText = review_date;
    const likeEl = document.createElement('a');
    likeEl.className = 'waves-effect waves-light btn btn-small like-review likerev-no';
    likeEl.innerText = 'LIKE';
    likeEl.id = 'el*' + review_id;
    const likeCountEl = document.createElement('span');
    likeCountEl.className = 'broj';
    likeCountEl.id = 'no' + review_id;
    likeCountEl.innerText = '(0)';
    reviewBlockEl.innerHTML += userImageEl.outerHTML + titleEl.outerHTML + paragraphEl.outerHTML
        + dateEl.outerHTML + likeEl.outerHTML + likeCountEl.outerHTML;
    return reviewBlockEl;
}