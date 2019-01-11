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
import isEqual from 'lodash/isEqual';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";

const api = new API();

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
            submitting: false,
            submitError: false,
        };
    }

    componentDidMount() {
        const { initialValues } = this.props;
        const { values } = this.state;
        !!initialValues && this.setState({values: {...values, ...initialValues}});
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { open, initialValues } = this.props;
        const { values } = this.state;

        if (!isEqual(prevProps.initialValues, initialValues) || (open === true && prevProps.open !== open)) {
            this.setState({values: {...values, ...initialValues}})
        }
    }

    handleChange = field => event => {
        const { values, errors } = this.state;
        delete(errors[field]);

        this.setState({
            values: {
                ...values,
                [field]: event.target.value,
            },
            errors,
        });
    };

    handleCancel = () => {
        this.setState({values: {}, errors: {}});
        this.props.onClose();
    };

    handleSubmit = event => {
        this.setState({
            submitting: true,
            submitError: false,
        });

        event.preventDefault();
        const { values } = this.state;

        /**
         * Create or update entity
         */
        values.id
            ? api.put(`housing/${values.id}`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit)
            : api.post(`housing/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
    };

    handleSuccessSubmit = () => {
        this.setState({submitting: false});
        this.props.onSuccess();
    };

    handleErrorSubmit = (err) => {
        this.setState({submitting: false, submitError: err.message});
    };

    render() {
        const { initialValues, open } = this.props;
        const { values, errors, submitting, submitError } = this.state;

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
                                    required
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
                                    required
                                    label={"Этажность"}
                                    type={"number"}
                                    inputProps={{ min: 1, max: 100, step: 1 }}
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
                                    required
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
                    {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
                </DialogContent>
                <DialogActions>
                    <Button
                        color={"primary"}
                        disabled={submitting}
                        onClick={this.handleCancel}
                    >
                        Отмена
                    </Button>
                    <Button
                        variant={"contained"}
                        color={"primary"}
                        form={"housing-form"}
                        type={"submit"}
                        disabled={submitting}
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

    /**
     * Fired when form success submitted
     */
    onSuccess: PropTypes.func.isRequired,
};

export default HousingForm;