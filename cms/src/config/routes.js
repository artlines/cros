import Home from '../pages/Home';
import Adobe from '../pages/Adobe/index';
import NotFound from '../pages/NotFound';
import RoomServiceIcon from '@material-ui/icons/RoomService';

export default [
    {
        path: '/cms',
        component: Home,
        exact: true,
        title: 'Главная',
    },
    {
        path: '/cms/adobe',
        component: Adobe,
        exact: true,
        title: 'Расселение',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Расселение',
        },
        role: 'ROLE_SETTLEMENT_MANAGER',
    },
    {
        path: '/cms/users',
        component: Adobe,
        exact: true,
        title: 'Пользователи',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Пользователи',
        },
        role: 'ROLE_ADMINISTRATOR',
    },
    {
        path: '*',
        component: NotFound,
        title: 'Страница не найдена',
    },
];