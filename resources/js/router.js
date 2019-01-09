import VueRouter from 'vue-router';
import Pages from './components/pages';

export default new VueRouter({
    routes: [
        {path: '/', redirect: '/teams'},
        {path: '/teams', component: Pages.TeamsPage},
        {path: '/teams/new', component: Pages.TeamFormPage},
        {path: '/teams/edit/:id', component: Pages.TeamFormPage},
        {path: '/players', component: Pages.PlayersPage},
        {path: '/players/new', component: Pages.PlayerFormPage},
        {path: '/players/edit/:id', component: Pages.PlayerFormPage},
        {path: '/login', component: Pages.LoginPage, name: 'login'},
        {path: '*', component: Pages.NotFoundPage}
    ]
});
