export default {
    state: {
        drawer: false,
        notification: {
            display: false,
            text: "Notification placeholder text",
            timeout: 3000,
            class: 'success'
        },
        newListForm: false,
        newMovieForm: false,
        imageFile: '',
        showType: 1
    },
    getters: {
        DRAWER: state => {
            return state.drawer;
        },
        IMAGE: state => {
            return state.imageFile;
        },
        NOTIFICATION: state => {
            return state.notification;
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
        SET_IMAGE: (state, payload) => {
            state.imageFile = payload;
        },
        SET_NOTIFICATION: (state, { display, text, alertClass }) => {
            state.notification.display = display;
            state.notification.text = text;
            state.notification.class = alertClass;
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