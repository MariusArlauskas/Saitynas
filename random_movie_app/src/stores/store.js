import Vue from 'vue';
import Vuex from 'vuex';

import UIModule from './modules/ui';
import User from './modules/user';
import Data from './modules/Data';
import DataMovies from './modules/DataMovies';
import DataUsers from './modules/DataUsers';
// import DataMyMovies from './modules/DataMyMovies';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        ui: UIModule,
        user: User,
        data: Data,
        dataMovies: DataMovies,
        dataUsers: DataUsers,
        // dataMyMovies: DataMyMovies
    }
});