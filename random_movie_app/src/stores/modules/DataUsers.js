import axios from "axios";

export default {
    state: {
        users: []
    },
    getters: {
        USERS: state => {
            return state.users;
        },
        USER_TITLE: state => index => {
            if (index) {
                return state.users.find(user => user.id === index)["username"];
            }
        },
        USER_MOVIES: state => index => {
            if (index) {
                return state.users.find(user => user.id === index).tasks;
            }
        }
    },
    mutations: {
        SET_USERS: (state, payload) => {
            state.users = payload;
        },
        ADD_USER: (state, payload) => {
            state.users.push(payload);
        },
        SET_USER_MOVIES: (state, { data, userId }) => {
            state.users.find(users => users.id === userId).tasks = data;
        }
    },
    actions: {
        GET_USERS: async ({ commit }) => {
            let { data } = await axios.get(`users`);
            commit("SET_USERS", data);
        },
        POST_USER: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.post(`users`, payload)
                    .then(({ data, status }) => {
                        let object = {
                            name: payload["name"],
                            description: "500",
                            id: data.split(" ")[data.split(" ").length - 1],
                            users_count: "0"
                        }
                        commit("ADD_USER", object)
                        if (status === 201) {
                            resolve({ object, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        GET_USER_MOVIES: async ({ commit }, payload) => {
            let { data } = await axios.get(`users/${payload}/movies`);
            commit("SET_USER_MOVIES", {
                data,
                userId: payload
            });
        }
    }
};