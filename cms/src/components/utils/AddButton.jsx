import React from 'react';
import PropTypes from 'prop-types';
import {Fab} from '@material-ui/core';
import {withTheme} from '@material-ui/core/styles';
import LibraryAddIcon from "@material-ui/icons/LibraryAdd";

class AddButton extends React.PureComponent {
    render() {
        const { theme, title, onClick } = this.props;

        return (
            <Fab
                size="medium"
                variant="extended"
                color="primary"
                aria-label="Add"
                onClick={onClick}
            >
                <LibraryAddIcon style={{ marginRight: theme.spacing.unit }}/>
                {title}
            </Fab>
        );
    }
}

AddButton.propTypes = {
    /**
     * MuiTheme object
     */
    theme: PropTypes.object.isRequired,
    /**
     * Button title
     */
    title: PropTypes.string.isRequired,
    /**
     * Fired when button is clicked
     */
    onClick: PropTypes.func.isRequired,
};

AddButton.defaultProps = {
    title: 'Добавить',
};

export default withTheme()(AddButton);