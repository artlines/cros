import React from "react";
import PropTypes from "prop-types";
import {Grid, Typography} from "@material-ui/core";
import ErrorIcon from "@material-ui/icons/Error";

class ErrorMessage extends React.PureComponent {
    render() {
        const { title, description, extended } = this.props;

        return (
            <Grid
                container
                alignItems={`flex-end`}
                spacing={8}
            >
                <Grid item>
                    <ErrorIcon style={{ color: "#e51c23", fontSize: "1.125rem" }}/>
                </Grid>
                <Grid item>
                    <Typography style={{ color: "#d50000", fontSize: "0.875rem" }} variant={"body2"}>
                        {title}
                    </Typography>
                </Grid>
                {extended &&
                <Grid item xs={12}>
                    <Typography style={{ color: "#d50000", fontSize: "0.875rem" }} variant={"body2"}>
                        {description}
                    </Typography>
                </Grid>}
            </Grid>
        );
    }
}

ErrorMessage.propTypes = {
    /**
     * Error title
     */
    title:          PropTypes.string.isRequired,
    /**
     * Extend information about error
     */
    description:    PropTypes.string.isRequired,
    /**
     * Show extend information?
     */
    extended:       PropTypes.bool.isRequired,
};

ErrorMessage.defaultProps = {
    title:          "Произошла ошибка",
    description:    "",
    extended:       false,
};

export default ErrorMessage;