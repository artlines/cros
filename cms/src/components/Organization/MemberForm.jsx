import React from "react";
import PropTypes from "prop-types";
import {connect} from 'react-redux';
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    TextField,
    MenuItem,
    LinearProgress,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';
import isEmpty from 'lodash/isEmpty';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";

const api = new API();

class MemberForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                first_name: '',
                last_name: '',
                middle_name: '',
                email: '',
                phone: '',
                post: '',
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

        /**
         * Check for updates initialValues
         */
        if (!isEqual(prevProps.initialValues, initialValues) || (open === true && prevProps.open !== open)) {
            this.setState({
                values: {...values, ...initialValues},
                submitError: false,
            })
        }
    }

    shouldComponentUpdate(nextProps, nextState, nextContext) {
        return nextProps.open || this.props.open;
    }

    handleChange = (field, index = null) => event => {
        const { values, errors } = this.state;
        const { name, value } = event.target;

        const update = index !== null ? { [field]: {...values[field], [index]: value }} : { [field]: value };

        index !== null ? (errors[field] && delete(errors[field][index])) : delete(errors[field]);
        isEmpty(errors[field]) && delete(errors[field]);

        this.setState({
            values: {
                ...values,
                ...update
            },
            errors,
            submitError: false,
        });
    };

    handleCancel = () => {
        this.props.onClose();
        this.setState({values: {}, errors: {}});
    };

    handleSubmit = event => {
        event.preventDefault();
        const { values } = this.state;
        const { initialValues } = this.props;

        this.setState({
            submitting: true,
            submitError: false,
        });

        /** Create or update entity */
        const id = initialValues && initialValues.id;
        if (!id) {
            api.post(`member/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        } else {
            api.put(`member/${id}`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        }
    };

    handleSuccessSubmit = () => {
        this.props.onSuccess();
        this.props.onClose();
        this.setState({
            values: {...this.initialValues},
            submitting: false
        });
    };

    handleErrorSubmit = (err) => this.setState({submitting: false, submitError: err.message});

    render() {
        const { initialValues, open } = this.props;
        const { values, errors, submitting, submitError } = this.state;

        const isUpdate = initialValues && initialValues.id;

        return (
            <Dialog
                open={open}
                fullWidth={true}
                maxWidth={"sm"}
            >
                {submitting && <LinearProgress/>}
                <DialogTitle>{isUpdate ? 'Редактирование' : 'Добавление'} участника</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"member-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Имя"}
                                    value={values.first_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'first_name'}
                                    onChange={this.handleChange('first_name')}
                                    error={!!errors.first_name}
                                    helperText={errors.first_name}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Фамилия"}
                                    value={values.last_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'last_name'}
                                    onChange={this.handleChange('last_name')}
                                    error={!!errors.last_name}
                                    helperText={errors.last_name}
                                />
                            </Grid>

                            <Grid item xs={12} sm={6}>
                                <TextField
                                    label={"Отчество"}
                                    value={values.middle_name}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'middle_name'}
                                    onChange={this.handleChange('middle_name')}
                                    error={!!errors.middle_name}
                                    helperText={errors.middle_name}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    label={"Должность"}
                                    value={values.post}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'post'}
                                    onChange={this.handleChange('post')}
                                    error={!!errors.post}
                                    helperText={errors.post}
                                />
                            </Grid>

                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Email"}
                                    value={values.email}
                                    margin={"dense"}
                                    type={`email`}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'email'}
                                    onChange={this.handleChange('email')}
                                    error={!!errors.email}
                                    helperText={errors.email}
                                />
                            </Grid>
                            <Grid item xs={12} sm={6}>
                                <TextField
                                    required
                                    label={"Телефон"}
                                    value={values.phone}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'phone'}
                                    onChange={this.handleChange('phone')}
                                    error={!!errors.phone}
                                    helperText={errors.phone}
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
                        form={"member-form"}
                        type={"submit"}
                        disabled={submitting}
                    >
                        {isUpdate ? 'Редактировать' : 'Добавить'}
                    </Button>
                </DialogActions>
            </Dialog>
        );
    }
}

MemberForm.propTypes = {
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

export default MemberForm;