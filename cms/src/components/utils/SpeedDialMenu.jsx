import React from 'react';
import PropTypes from 'prop-types';
import {
    SpeedDial,
    SpeedDialIcon,
    SpeedDialAction,
} from '@material-ui/lab';

const style = {
    position: 'fixed',
    bottom: '30px',
    right: '20px',
};

function SpeedDialMenu({ actions, hidden }) {
    const [open, setOpen] = React.useState(false);

    function handleOpen() {
        !hidden && setOpen(true);
    }

    function handleClose() {
        setOpen(false);
    }

    function handleClick() {
        setOpen(!open);
    }

    return (
        <SpeedDial
            style={style}
            ariaLabel={`Меню`}
            open={open}
            hidden={hidden}
            icon={<SpeedDialIcon/>}
            onBlur={handleClose}
            onFocus={handleOpen}
            onClick={handleClick}
            onMouseEnter={handleOpen}
            onMouseLeave={handleClose}
        >
            {actions.map(action =>
                <SpeedDialAction
                    key={action.title}
                    icon={action.icon}
                    tooltipTitle={action.title}
                    tooltipOpen={!!action.tooltipOpen}
                    onClick={() => {
                        action.onClick();
                        handleClose();
                    }}
                />
            )}
        </SpeedDial>
    );
}

SpeedDialMenu.propTypes = {
    actions: PropTypes.arrayOf(
        PropTypes.shape({
            icon:           PropTypes.object.isRequired,
            title:          PropTypes.string.isRequired,
            onClick:        PropTypes.func.isRequired,
            tooltipOpen:    PropTypes.bool,
        }),
    ),
};

export default SpeedDialMenu;