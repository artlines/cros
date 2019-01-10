import Home from '../pages/Home';
import AdobeIndex from '../pages/Adobe/index';
import AdobeSettings from '../pages/Adobe/Settings';
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
        path: '/cms/adobe',
        component: AdobeIndex,
        exact: true,
        title: 'Расселение',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Расселение',
        },
        role: 'ROLE_SETTLEMENT_MANAGER',
    },
    {
        path: '/cms/adobe/settings',
        component: AdobeSettings,
        exact: true,
        title: 'Настройка расселения',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Настройка расселения',
        },
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