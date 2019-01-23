import Home from "../pages/Home";
import AbodeIndex from "../pages/Abode/index";
import AbodeSettings from "../pages/Abode/Settings";
import AbodeHousing from "../pages/Abode/Housing";
import Organizations from "../pages/Organizations";
import Invite from "../pages/Invite";
import NotFound from "../pages/NotFound";
import {
    PeopleOutlined as People,
    BusinessCenterOutlined as BusinessCenter,
    GroupAddOutlined as GroupAdd,
    LocationCityOutlined as LocationCity,
    DashboardOutlined as Dashboard,
} from "@material-ui/icons";

export default [
    {
        path: "/cms/",
        Component: Home,
        exact: true,
        title: "Главная",
        menuItem: {
            Icon: Dashboard,
            title: "Главная",
        },
    },
    {
        path: "/cms/abode",
        Component: AbodeIndex,
        exact: true,
        title: "Расселение участников",
        menuItem: {
            Icon: LocationCity,
            title: "Расселение участников",
        },
        role: "ROLE_SETTLEMENT_MANAGER",
    },
    // {
    //     path: "/cms/abode/settings",
    //     Component: AbodeSettings,
    //     exact: true,
    //     title: "Расселение",
    //     menuItem: {
    //         Icon: RoomServiceIcon,
    //         title: "Настройка расселения",
    //     },
    //     role: "ROLE_SETTLEMENT_MANAGER",
    // },
    {
        path: "/cms/abode/housing/:id",
        Component: AbodeHousing,
        exact: true,
        title: "Расселение",
        role: "ROLE_SETTLEMENT_MANAGER",
    },
    {
        path: "/cms/organizations",
        Component: Organizations,
        exact: true,
        title: "Организации",
        menuItem: {
            Icon: BusinessCenter,
            title: "Организации",
        },
        role: "ROLE_SETTLEMENT_MANAGER",
    },
    {
        path: "/cms/invite",
        Component: Invite,
        exact: true,
        title: "Рассылка приглашений",
        menuItem: {
            Icon: GroupAdd,
            title: "Рассылка приглашений",
        },
        role: "ROLE_SALES_MANAGER",
    },
    {
        path: "/cms/users",
        Component: Home,
        exact: true,
        title: "Пользователи",
        menuItem: {
            Icon: People,
            title: "Пользователи",
        },
        role: "ROLE_ADMINISTRATOR",
    },
    {
        path: "*",
        Component: NotFound,
        title: "Страница не найдена",
    },
];