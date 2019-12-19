import axios from 'axios';

export default {
    state: {
        role: ''
    },
    getters: {
        ROLE: state => {
            return state.role;
        }
    },
    mutations: {
        SET_ROLE: (state, payload) => {
            state.role = payload;
        },
    },
    actions: {
        LOGIN: (commit, payload) => {
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
        SET_ROLE: async ({commit}) => {
            await axios.get(`profile/role`)
                .then(({ data, status }) => {
                    if (status === 200) {
                        commit("SET_ROLE", data)
                    }
                })
        },
        LOGOUT: () => {
            return new Promise((resolve, reject) => {
                axios.post(`logout`)
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
        REGISTER: (commit, { username, email, password }) => {
            return new Promise((resolve, reject) => {
                axios.post(`register`, {
                    username, email, password
                })
                    .then(({ status }) => {
                        if (status === 200) {
                            resolve(true)
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        REFRESH_TOKEN: () => {
            return new Promise((resolve, reject) => {
                axios.post(`token/refresh`)
                    .then(response => {
                        resolve(response);
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        }
    }
}