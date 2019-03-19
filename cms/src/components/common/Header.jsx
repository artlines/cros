import React from "react";
import PropTypes from "prop-types";
import {
    AppBar,
    Typography,
    IconButton,
    Toolbar,
    Tooltip,
} from "@material-ui/core";
import MenuIcon from "@material-ui/icons/Menu";
import ExitToAppIcon from "@material-ui/icons/ExitToApp";
import PowerSettingsNewIcon from "@material-ui/icons/PowerSettingsNew";
import ReplyIcon from "@material-ui/icons/Reply";
import { withTheme } from "@material-ui/core/styles";

class Header extends React.PureComponent {
    render() {
        const { theme, handleClickMenu, title } = this.props;

        return (
            <AppBar color={`default`} position={`static`}>
                <Toolbar>
                    <IconButton onClick={handleClickMenu}>
                        <MenuIcon/>
                    </IconButton>
                    <Typography variant={`h6`} noWrap color={`inherit`} style={{ marginLeft: theme.spacing.unit * 2 }}>
                        {title}
                    </Typography>
                    <div style={{flexGrow: 1}}/>
                    <div>
                        <Tooltip title={`В старую CMS`}>
                            <a href={`/admin`}>
                                <IconButton>
                                    <ReplyIcon/>
                                </IconButton>
                            </a>
                        </Tooltip>
                        <Tooltip title={`На сайт`}>
                            <a href={`/`}>
                                <IconButton>
                                    <ExitToAppIcon/>
                                </IconButton>
                            </a>
                        </Tooltip>
                        <Tooltip title={`Выйти`}>
                            <a href={`/logout`}>
                                <IconButton>
                                    <PowerSettingsNewIcon/>
                                </IconButton>
                            </a>
                        </Tooltip>
                    </div>
                </Toolbar>
            </AppBar>
        );
    }
}

Header.propTypes = {
    /**
     * Fired on click on Menu
     */
    handleClickMenu: PropTypes.func.isRequired,

    /**
     * Page title
     */
    title: PropTypes.string.isRequired,
};

export default withTheme()(Header);