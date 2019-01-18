import React from "react";
import PropTypes from "prop-types";
import {Fab} from "@material-ui/core";
import {withTheme} from "@material-ui/core/styles";
import AddIcon from "@material-ui/icons/Add";

class FabButton extends React.PureComponent {
    render() {
        const { theme, title, onClick, IconComponent } = this.props;

        return (
            <Fab
                size="medium"
                variant="extended"
                color="primary"
                aria-label="Add"
                onClick={onClick}
            >
                {IconComponent &&
                    <IconComponent style={{ marginRight: theme.spacing.unit }}/>
                }
                {title}
            </Fab>
        );
    }
}

FabButton.propTypes = {
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
    /**
     * Button icon
     */
    IconComponent: PropTypes.oneOfType([PropTypes.func, PropTypes.bool]),
};

FabButton.defaultProps = {
    title: "Добавить",
    IconComponent: AddIcon,
};

export default withTheme()(FabButton);