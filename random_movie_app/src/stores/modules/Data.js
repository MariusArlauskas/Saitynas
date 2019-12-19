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
        LIST: state => index => {
            if (index) {
                return state.lists.find(list => list.id === index);
            }
        },
        TASKS: state => index => {
            if (index) {
                return state.lists.find(list => list.id === index).movies;
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
            state.lists.find(lists => lists.id === listId).movies = data;
        },
        REMOVE_MOVIE_BY_INDEX: (state, payload) => {
            var removeIndex = state.lists.map(function (item) { return item.id; })
                .indexOf(payload);
            state.lists.splice(removeIndex, 1);
        },
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
                        commit("ADD_LIST", data)
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        UPDATE_GENRE: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.put(`genres/${payload.id}`, payload)
                    .then(({ data, status }) => {
                        if (commit && (true == false)) { return }
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        DELETE_GENRE: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.delete(`genres/${payload.genreId}`)
                    .then(({ data, status }) => {
                        commit("REMOVE_GENRE_BY_INDEX", payload.genreId)
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        GET_TASKS: async ({ commit }, payload) => {
            try {
                let { data } = await axios.get(`genres/${payload}/movies`);
                commit("SET_TASKS", {
                    data,
                    listId: payload
                });
            } catch (error) {
                console.log("No movies!")
            }
        }
    }
};