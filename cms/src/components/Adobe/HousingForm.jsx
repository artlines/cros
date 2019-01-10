import React from "react";
import PropTypes from "prop-types";
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    TextField,
} from '@material-ui/core';

/**
 * title
 * description
 * num_of_floors
 */

class HousingForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {},
            errors: {},
        };
    }

    componentDidMount() {
        const { initialValues } = this.props;

        !!initialValues && this.setState({values: {...values, initialValues}});
    }

    render() {
        const { initialValues, open } = this.props;
        const { values, errors } = this.state;

        return (
            <Dialog
                open={open}
                fullWidth={true}
                maxWidth={"sm"}
            >
                <DialogTitle>{!initialValues ? 'Добавление' : 'Изменение'} корпуса</DialogTitle>
                <DialogContent>
                    <Grid container spacing={16}>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                label={"Наименование"}
                                value={values.title}
                                margin={"dense"}
                                fullWidth
                                variant={"outlined"}
                            />
                        </Grid>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                label={"Этажность"}
                                type={"number"}
                                value={values.num_of_floors}
                                margin={"dense"}
                                fullWidth
                                variant={"outlined"}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <TextField
                                label={"Описание"}
                                value={values.description}
                                margin={"dense"}
                                fullWidth
                                variant={"outlined"}
                                multiline
                                rows={3}
                                rowsMax={5}
                            />
                        </Grid>
                    </Grid>
                </DialogContent>
                <DialogActions>
                    <Button color={"primary"}>Cancel</Button>
                    <Button variant={"contained"} color={"primary"}>Save</Button>
                </DialogActions>
            </Dialog>
        );
    }
}

HousingForm.propTypes = {
    /**
     * Is form open?
     */
    open: PropTypes.bool.isRequired,
    /**
     * Initial form values
     */
    initialValues: PropTypes.object,
};

export default HousingForm;