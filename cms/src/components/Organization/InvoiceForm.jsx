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
    MenuItem,
    LinearProgress,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';
import isEmpty from 'lodash/isEmpty';
import API from '../../libs/api';
import ErrorMessage from "../utils/ErrorMessage";

const api = new API();

class InvoiceForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                number: '',
                amount: '',
                date: Date.now(),
                status: 1,
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
            api.post(`invoice/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
        } else {
            api.put(`invoice/${id}`, values)
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
                <DialogTitle>{isUpdate ? 'Редактирование' : 'Добавление'} счета</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"invoice-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Номер счета"}
                                    type={"number"}
                                    value={values.number}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'number'}
                                    onChange={this.handleChange('number')}
                                    error={!!errors.number}
                                    helperText={errors.number}
                                />
                            </Grid>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Дата оплаты"}
                                    type={"date"}
                                    value={values.date}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'date'}
                                    onChange={this.handleChange('date')}
                                    error={!!errors.date}
                                    helperText={errors.date}
                                />
                            </Grid>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Сумма"}
                                    type={"number"}
                                    value={values.amount}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'amount'}
                                    onChange={this.handleChange('amount')}
                                    error={!!errors.amount}
                                    helperText={errors.amount}
                                />
                            </Grid>
                            <Grid item xs={12} sm={4}>
                                <TextField
                                    required
                                    label={"Статус оплаты"}
                                    value={values.status}
                                    margin={"dense"}
                                    fullWidth
                                    variant={"outlined"}
                                    name={'type'}
                                    onChange={this.handleChange('status')}
                                    error={!!errors.status}
                                    helperText={errors.status}
                                    select={true}
                                >
                                    <MenuItem value={1}>Не оплачен</MenuItem>
                                    <MenuItem value={2}>Частично оплачен</MenuItem>
                                    <MenuItem value={3}>Полностью оплачен</MenuItem>
                                </TextField>
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
                        form={"invoice-form"}
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

InvoiceForm.propTypes = {
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

export default InvoiceForm;