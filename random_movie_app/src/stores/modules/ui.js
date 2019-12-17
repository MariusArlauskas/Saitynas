export default {
    state: {
        drawer: false,
        notification: {
            display: false,
            text: "Notification placeholder text",
            timeout: 3000,
            class: 'success'
        },
        displaySearchList: false,
        newListForm: false,
        newMovieForm: false,
        showType: 1
    },
    getters: {
        DRAWER: state => {
            return state.drawer;
        },
        NOTIFICATION: state => {
            return state.notification;
        },
        DISPLAY_SEARCH_LIST: state => {
            return state.displaySearchList;
        },
        SHOW_TYPE: state => {
            return state.showType;
        },
        NEW_LIST_FORM: state => {
            return state.newListForm;
        },
        NEW_MOVIE_FORM: state => {
            return state.newMovieForm;
        }
    },
    mutations: {
        SET_DRAWER: (state, payload) => {
            state.drawer = payload;
        },
        SET_NOTIFICATION: (state, { display, text, alertClass }) => {
            state.notification.display = display;
            state.notification.text = text;
            state.notification.class = alertClass;
        },
        SET_DISPLAY_SEARCH_LIST: (state, payload) => {
            state.displaySearchList = payload;
        },
        SET_SHOW_TYPE: (state, payload) => {
            state.showType = payload;
        },
        SET_NEW_LIST_FORM: (state, payload) => {
            state.newListForm = payload;
        },
        SET_NEW_MOVIE_FORM: (state, payload) => {
            state.newMovieForm = payload;
        }
    },
    actions: {

    }
}