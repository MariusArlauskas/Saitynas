import axios from "axios";

export default {
    state: {
        lists: []
    },
    getters: {
        LISTS: state => {
            return state.lists;
        },
        LISTS_TITLES: state => {
            let temp = []
            state.lists.forEach(item => {
                let name = item.name;
                let id = item.id;
                let description = item.description;
                temp.push({ name, id, description });
            });
            return temp;
        },
        LIST_TITLE: state => index => {
            if (index) {
                return state.lists.find(list => list.id === index)["name"];
            }
        },
        LIST: state => index => {
            if (index) {
                return state.lists.find(list => list.id === index);
            }
        },
        TASKS: state => index => {
            if (index) {
                return state.lists.find(list => list.id === index).tasks;
            }
        }
    },
    mutations: {
        SET_LISTS: (state, payload) => {
            state.lists = payload;
        },
        ADD_LIST: (state, payload) => {
            state.lists.push(payload);
        },
        SET_TASKS: (state, { data, listId }) => {
            state.lists.find(lists => lists.id === listId).tasks = data;
        }
    },
    actions: {
        GET_LISTS: async ({ commit }) => {
            let { data } = await axios.get(`genres`);
            commit("SET_LISTS", data);
        },
        POST_LIST: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.post(`genres`, payload)
                    .then(({ data, status }) => {
                        let object = {
                            name: payload["name"],
                            description: "500",
                            id: data.split(" ")[data.split(" ").length - 1],
                            movies_count: "0"
                        }
                        commit("ADD_LIST", object)
                        if (status === 201) {
                            resolve({ object, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        GET_TASKS: async ({ commit }, payload) => {
            let { data } = await axios.get(`genres/${payload}/movies`);
            commit("SET_TASKS", {
                data,
                listId: payload
            });
        }
    }
};