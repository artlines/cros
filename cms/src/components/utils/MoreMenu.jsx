import React from 'react';
import PropTypes from 'prop-types';
import { withRouter } from 'react-router-dom';
import {
    Menu,
    MenuItem,
    IconButton,
    ListItemIcon,
} from '@material-ui/core';
import {
    MoreVert as MoreVertIcon,
} from '@material-ui/icons';

function MoreMenu({ items, history }) {
    const [anchorEl, setAnchor] = React.useState(null);
    const open = Boolean(anchorEl);

    function handleClick(event) {
        setAnchor(event.currentTarget);
    }

    function handleClose() {
        setAnchor(null);
    }

    function handleClickItem(item) {
        item.onClick && item.onClick();
        item.href && history.push(item.href);
    }

    return (
        <React.Fragment>
            <IconButton onClick={handleClick}>
                <MoreVertIcon/>
            </IconButton>
            <Menu anchorEl={anchorEl} open={open} onClose={handleClose}>
                {items.map((i, key) =>
                    <MenuItem key={key} onClick={() => handleClickItem(i)}>
                        {i.icon
                            ? <ListItemIcon>
                                <i.icon/>
                            </ListItemIcon>
                            : ''
                        }
                        {i.title}
                    </MenuItem>
                )}
            </Menu>
        </React.Fragment>
    );
}

MoreMenu.propTypes = {
    items: PropTypes.arrayOf(
        PropTypes.shape({
            title: PropTypes.string.isRequired,
            icon: PropTypes.func,
            onClick: PropTypes.func,
            href: PropTypes.string,
        })
    ),
};

export default withRouter(MoreMenu);