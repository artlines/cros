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
import ReplyIcon from "@material-ui/icons/Reply";
import { withTheme } from "@material-ui/core/styles";

const btnStyle = { color: 'white' };

class Header extends React.PureComponent {
    render() {
        const { theme, handleClickMenu, title } = this.props;

        return (
            <AppBar color={`default`} position={`static`}>
                <Toolbar
                    style={{
                        background: 'linear-gradient(to left top, rgb(92, 78, 144), rgb(36, 170, 136))',
                        color: 'white',
                    }}
                >
                    <IconButton onClick={handleClickMenu}>
                        <MenuIcon style={btnStyle}/>
                    </IconButton>
                    <Typography variant={`h6`} noWrap color={`inherit`} style={{ marginLeft: theme.spacing.unit * 2 }}>
                        {title}
                    </Typography>
                    <div style={{flexGrow: 1}}/>
                    <div>
                        <Tooltip title={`В старую CMS`}>
                            <a href={`/admin`}>
                                <IconButton style={btnStyle}>
                                    <ReplyIcon/>
                                </IconButton>
                            </a>
                        </Tooltip>
                        <Tooltip title={`На сайт`}>
                            <a href={`/`}>
                                <IconButton style={btnStyle}>
                                    <ExitToAppIcon/>
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