import request from 'superagent';
import map from 'lodash/map'
import isArray from 'lodash/isArray'

const errorHandler = Symbol('errorHandler');

export default class API {

    constructor() {
        this.host = window.location.origin;
        this.apiVersion = 1;

        this.authPath = `${this.host}/auth`;
        this.apiHost = `${this.host}/api/v${this.apiVersion}`;

        this.defaultOffset = 0;
        this.defaultLimit = 10;

        this.defaultOptions = {
            credentials: 'include'
        };
        this.defaultHeaders = {
            "Content-type": 'application/x-www-form-urlencoded; charset=UTF-8',
        };
    }

    [errorHandler](err) {
        switch (err.status) {
            case 401:
                window.location.replace(this.authPath);
            default:
                break;
        }
    }

    get = async (alias, data = {}) => {
        try {
            const response = await request
                .get(`${this.apiHost}/${alias}`)
                .query(data)
                .type('json');
            return response.body;
        } catch (err) {
            this[errorHandler](err);
        }
    };

    get2 = async (alias, data = {}) => {
        const options = {
            ...this.defaultOptions,
            headers: {...this.defaultHeaders},
        };

        let url = `${this.apiHost}/${alias}?`;
        let query = '';

        // check for 'filter' prop to 'where' condition
        query += data.filter
            ? map(data.filter, (v, k) =>
            isArray(v)
                ? v.map(_v => `${k}[]=${_v}`).join('&')
                : `${k}=${v}`
        ).join('&') + `&`
            : '';

        // 'limit' prop to 'limit' condition
        query += `@limit=${data.limit ? data.limit : this.defaultLimit}&`;

        // 'offset' prop to 'offset' condition
        query += `@offset=${data.offset ? data.offset : this.defaultOffset}&`;

        // check for 'sort' prop to 'orderBy' condition
        query += data.sort ? map(data.sort, (order, field) => `@sort-${field}=${order}&`) : '';

        url += query;

        const response = await fetch(url, options);

        switch (response.status) {
            case 403:
                return window.location = `${this.host}/auth`;

            default:
                return response;
        }
    };

    post = async (alias, data = {}) => {

        const options = {
            ...this.defaultOptions,
            headers:    {...this.defaultHeaders},
            method:     'POST',
            body:       JSON.stringify(data),
        };

        const url = `${this.apiHost}/${alias}`;

        return await fetch(url, options)
    };

    delete = async (alias) => {

        const options = {
            ...this.defaultOptions,
            headers:    {...this.defaultHeaders},
            method:     'DELETE',
        };

        const url = `${this.apiHost}/${alias}`;

        return await fetch(url, options)
    };
}