import Home from "../pages/Home";
import AbodeIndex from "../pages/Abode/index";
import AbodeSettings from "../pages/Abode/Settings";
import AbodeHousing from "../pages/Abode/Housing";
import Organizations from "../pages/Organizations";
import NotFound from "../pages/NotFound";
import {
    RoomService as RoomServiceIcon,
    BusinessCenterTwoTone,
} from "@material-ui/icons";

export default [
    {
        path: "/cms/",
        Component: Home,
        exact: true,
        title: "Главная",
        menuItem: {
            Icon: RoomServiceIcon,
            title: "Главная",
        },
    },
    {
        path: "/cms/abode",
        Component: AbodeIndex,
        exact: true,
        title: "Расселение",
        menuItem: {
            Icon: RoomServiceIcon,
            title: "Расселение",
        },
        role: "ROLE_SETTLEMENT_MANAGER",
    },
    {
        path: "/cms/abode/settings",
        Component: AbodeSettings,
        exact: true,
        title: "Расселение",
        menuItem: {
            Icon: RoomServiceIcon,
            title: "Настройка расселения",
        },
        role: "ROLE_SETTLEMENT_MANAGER",
    },
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
            Icon: BusinessCenterTwoTone,
            title: "Организации",
        },
        role: "ROLE_ADMINISTRATOR",
    },
    {
        path: "/cms/users",
        Component: Home,
        exact: true,
        title: "Пользователи",
        menuItem: {
            Icon: RoomServiceIcon,
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