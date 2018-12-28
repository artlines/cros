import Home from '../pages/Home';
import Adobe from '../pages/Adobe';
import NotFound from '../pages/NotFound';
import RoomServiceIcon from '@material-ui/icons/RoomService';

export default [
    {
        path: '/cms',
        component: Home,
        exact: true,
        title: '',
    },
    {
        path: '/cms/adobe',
        component: Adobe,
        exact: true,
        title: 'Расселение',
        menuItem: {
            Icon: RoomServiceIcon,
            title: 'Расселение',
        }
    },
    {
        path: '*',
        component: NotFound,
        title: 'Страница не найдена',
    },
];