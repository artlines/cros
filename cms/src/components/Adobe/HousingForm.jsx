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
            values: {
                title: '',
                description: '',
                num_of_floors: '',
            },
            errors: {},
        };
    }

    componentDidMount() {
        const { initialValues } = this.props;
        !!initialValues && this.setState({values: {...values, initialValues}});
    }

    handleChange = field => event => {
        console.log(`HousingForm::handleChange`, {
            field,
            value: event.target.value,
        });

        const { values, errors } = this.state;

        this.setState({
            values: {
                ...values,
                [field]: event.target.value,
            },
            errors: {
                ...errors,
                [field]: '',
            }
        });
    };

    handleCancel = () => {
        this.setState({values: {}, errors: {}});
        this.props.onClose();
    };

    handleSubmit = values => {
        console.log(`HousingForm::handleSubmit`, values);
    };

    componentWillUnmount() {
        console.log(`HousingForm::componentWillUnmount`);
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
                <DialogTitle>{!initialValues ? 'Добавление' : 'Редактирование'} корпуса</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"housing-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    label={"Наименование"}
                                    value={values.title}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChange('title')}
                                    error={!!errors.title}
                                    helperText={errors.title}
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
                                    onChange={this.handleChange('num_of_floors')}
                                    error={!!errors.num_of_floors}
                                    helperText={errors.num_of_floors}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    label={"Описание"}
                                    value={values.description}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    onChange={this.handleChange('description')}
                                    error={!!errors.description}
                                    helperText={errors.description}
                                    multiline
                                    rows={3}
                                    rowsMax={5}
                                />
                            </Grid>
                        </Grid>
                    </form>
                </DialogContent>
                <DialogActions>
                    <Button
                        color={"primary"}
                        onClick={this.handleCancel}
                    >
                        Отмена
                    </Button>
                    <Button
                        variant={"contained"}
                        color={"primary"}
                        form={"housing-form"}
                    >
                        {!initialValues ? 'Добавить' : 'Сохранить'}
                    </Button>
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

    /**
     * Fired when form need to be closed
     */
    onClose: PropTypes.func.isRequired,
};

export default HousingForm;