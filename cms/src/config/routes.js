import Home from "../pages/Home";
import AbodeIndex from "../pages/Abode/index";
import AbodeSettings from "../pages/Abode/Settings";
import AbodeResettlement from "../pages/Abode/Resettlement";
import AbodeHousing from "../pages/Abode/Housing";
import Organizations from "../pages/Organizations";
import Invite from "../pages/Invite";
import Users from "../pages/Users";
import Conferences from "../pages/Conferences";
import Archive from "../pages/Conference/Archive";
import Program from "../pages/Program";
import Report from "../pages/Report";
import NotFound from "../pages/NotFound";
import {
    PeopleOutlined as People,
    BusinessCenterOutlined as BusinessCenter,
    GroupAddOutlined as GroupAdd,
    LocationCityOutlined as LocationCity,
    DashboardOutlined as Dashboard,
    SettingsApplicationsOutlined as SettingsApplications,
    EventSeatOutlined as EventSeat,
    EventNoteOutlined as EventNote,
    FilterListOutlined as FilterList,
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
        path: "/cms/conferences",
        Component: Conferences,
        exact: true,
        title: "Конференции",
        menuItem: {
            Icon: EventSeat,
            title: "Конференции",
        },
        role: "ROLE_CONTENT_MANAGER",
    },
    {
        path: "/cms/conference/:id/archive",
        Component: Archive,
        exact: true,
        title: "Архив",
        role: "ROLE_CONTENT_MANAGER",
    },
    {
        path: "/cms/program",
        Component: Program,
        exact: true,
        title: "Программа",
        menuItem: {
            Icon: EventNote,
            title: "Программа",
        },
        role: "ROLE_CONTENT_MANAGER",
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
    {
        path: "/cms/abode/settings",
        Component: AbodeSettings,
        exact: true,
        title: "Настройка расселения",
        menuItem: {
            Icon: SettingsApplications,
            title: "Настройка расселения",
        },
        role: "ROLE_SETTLEMENT_MANAGER",
    },
    {
        path: "/cms/abode/housing/:id/apartments",
        Component: AbodeHousing,
        exact: true,
        title: "Расселение участников",
        role: "ROLE_SETTLEMENT_MANAGER",
    },
    {
        path: "/cms/abode/housing/:id/resettlement",
        Component: AbodeResettlement,
        exact: true,
        title: "Расселение участников",
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
        Component: Users,
        exact: true,
        title: "Пользователи",
        menuItem: {
            Icon: People,
            title: "Пользователи",
        },
        role: "ROLE_ADMINISTRATOR",
    },
    {
        path: "/cms/report",
        Component: Report,
        exact: true,
        title: "Отчеты",
        menuItem: {
            Icon: FilterList,
            title: "Отчеты",
        },
        role: "ROLE_SETTLEMENT_MANAGER",
    },
    {
        path: "*",
        Component: NotFound,
        title: "Страница не найдена",
    },
];