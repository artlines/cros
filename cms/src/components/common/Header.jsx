import React from 'react';
import PropTypes from 'prop-types';
import {
    AppBar,
    Typography,
    IconButton,
    Toolbar,
} from '@material-ui/core';
import MenuIcon from '@material-ui/icons/Menu';
import { withTheme } from '@material-ui/core/styles';
import {Link} from 'react-router-dom';

class Header extends React.PureComponent {

    render() {
        const { theme, handleClickMenu } = this.props;

        return (
            <AppBar color={`default`} position={`static`}>
                <Toolbar>
                    <IconButton onClick={handleClickMenu}>
                        <MenuIcon/>
                    </IconButton>
                    <Typography variant={`h6`} noWrap color={`inherit`} style={{ marginLeft: theme.spacing.unit * 2 }}>
                        <Link to={`/cms`}>CROS</Link>
                    </Typography>
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
};

export default withTheme()(Header);