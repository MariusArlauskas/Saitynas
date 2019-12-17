import axios from 'axios';

export default {
    state: {},
    getters: {},
    mutations: {},
    actions: {
        LOGIN: (commit,payload) => {
            return new Promise((resolve, reject) => {
                 axios
                    .post(`login_check`, payload)
                    .then(({ status }) => {
                        if (status === 200) {
                            resolve(true);
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        REGISTER: (commit,{username, email, password}) => {
            return new Promise((resolve, reject) => {
                axios.post(`register`, {
                    username, email, password
                })
                .then(({status}) => {
                    if (status === 200) {
                        resolve(true)
                    }
                })
                .catch(error => {
                    reject(error);
                })
            })
        }
    }
}