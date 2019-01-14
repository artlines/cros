import Home from '../pages/Home';
import AbodeIndex from '../pages/Abode/index';
import AbodeSettings from '../pages/Abode/Settings';
import AbodeHousing from '../pages/Abode/Housing';
import NotFound from '../pages/NotFound';
import RoomServiceIcon from '@material-ui/icons/RoomService';

export default [
    {
        path: '/cms/',
        component: Home,
        exact: true,
        title: 'Главная',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Главная',
        },
    },
    {
        path: '/cms/abode',
        component: AbodeIndex,
        exact: true,
        title: 'Расселение',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Расселение',
        },
        role: 'ROLE_SETTLEMENT_MANAGER',
    },
    {
        path: '/cms/abode/settings',
        component: AbodeSettings,
        exact: true,
        title: 'Расселение',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Настройка расселения',
        },
        role: 'ROLE_SETTLEMENT_MANAGER',
    },
    {
        path: '/cms/abode/housing/:id',
        component: AbodeHousing,
        exact: true,
        title: 'Расселение',
        role: 'ROLE_SETTLEMENT_MANAGER',
    },
    {
        path: '/cms/users',
        component: Home,
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