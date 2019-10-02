export function WebHelper () {
     this.baseUrl = () => {
        let urlTokens = window.location.href.split('/');
        let baseUrl = '';
        for (let i = 0; i < urlTokens.length - 2; i++) {
            baseUrl += urlTokens[i] + '/';
        }
        return baseUrl
    }

    this.generateEndpoint = (slug) => {
        return this.baseUrl() + slug;
    }
}
