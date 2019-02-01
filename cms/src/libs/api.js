import request from 'superagent';

const errorHandler = Symbol('errorHandler');

export default class API {

    constructor() {
        this.host = window.location.origin;
        this.apiVersion = 1;

        this.authPath = `${this.host}/auth`;
        this.apiHost = `${this.host}/api/v${this.apiVersion}`;
    }

    get = async (endPoint, data = {}) => {
        try {
            const response = await request
                .get(`${this.apiHost}/${endPoint}`)
                .query(data);
            return response.body;
        } catch (err) {
            return this[errorHandler](err);
        }
    };

    post = (endPoint, data) => {
        return request
            .post(`${this.apiHost}/${endPoint}`)
            .send(data)
            .catch(this[errorHandler])
    };

    put = (endPoint, data) => {
        return request
            .put(`${this.apiHost}/${endPoint}`)
            .send(data)
            .catch(this[errorHandler])
    };

    delete = (endPoint) => {
        return request
            .del(`${this.apiHost}/${endPoint}`)
            .catch(this[errorHandler])
    };

    upload = (endPoint, formData) => {
        return request
            .post(`${this.apiHost}/${endPoint}`)
            .send(formData)
            .catch(this[errorHandler]);

    };

    [errorHandler](err) {
        let errorMessage = err.message;

        switch (err.status) {
            case 401:
                window.location.replace(this.authPath);
                break;
            default:
                if (err.response.body && err.response.body.error) {
                    errorMessage = err.response.body.error;
                }
                break;
        }

        throw Error(errorMessage);

        //return { error: errorMessage };
    }
}