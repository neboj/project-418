export function WebHelper () {
     this.baseUrl = () => {
        let urlTokens = window.location.href.split('/');
        let baseUrl = '';
        let lastToken = 'app_dev.php';
        for (let i of urlTokens) {
            baseUrl += i + '/';
            if (i === lastToken) {
                return baseUrl;
            }
        }
        return baseUrl
    }

    this.generateEndpoint = (slug) => {
        return this.baseUrl() + slug;
    }
}
