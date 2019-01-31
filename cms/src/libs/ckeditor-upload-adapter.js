import API from './api';

class UploadAdapter {
    constructor(loader) {
        this.loader = loader;
        this.api = new API();
    }

    upload() {
        const data = new FormData();
        data.append('typeOption', 'file');
        data.append('file', this.loader.file);

        return new Promise((resolve, reject) => {
            this.api
                .upload(`attachment/upload_public`, data)
                .then(res => {
                    const resData = res.body;
                    resData.default = resData.url;
                    resolve(resData);
                })
                .catch(error => {
                    reject(error);
                });
        });
    }
}

export default UploadAdapter;