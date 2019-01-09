import Vuex from 'vuex';
import TeamsModule from './modules/teams-module';
import PlayersModule from './modules/players-module';
import AuthModule from './modules/auth-module';

const debug = true;

export default new Vuex.Store({
    modules: {
        teams: new TeamsModule(),
        players: new PlayersModule(),
        auth: new AuthModule(),
    },
    strict: debug,
});
