/**
 * Get users info
 * @constructor
 */
export function Profile() {
    let _name = document.getElementsByClassName('profile-name-container')[0].innerText; //private variable
    let _image_element = document.getElementsByClassName('profile-image-container')[0].firstElementChild;   //private variable
    let _image_src = _image_element.src; //private variable

    this.getImageElement = function() { // public
        return _image_element;
    };

    this.getName = function () {      // public
        return _name;
    };

    this.getImageUrl = function () {    //  public
        return _image_src;
    };
}