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
import API from '../../../libs/api';
import ErrorMessage from "../../utils/ErrorMessage";
import ConfirmDialog from "../../utils/ConfirmDialog";

const api = new API();

class ParticipationClassForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                title: '',
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
            submitError: false,
        });
    };

    handleCancel = () => {
        this.setState({values: {}, errors: {}, submitError: false});
        this.props.onClose();
    };

    handleSubmit = event => {
        event.preventDefault();

        this.setState({
            submitting: true,
            submitError: false,
        });

        const { values } = this.state;

        /**
         * Create or update entity
         */
        values.id
            ? api.put(`participation_class/${values.id}`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit)
            : api.post(`participation_class/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
    };

    handleDelete = (id) => {
        api.delete(`participation_class/${id}`)
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit);
    };

    handleSuccessSubmit = () => {
        this.setState({submitting: false});
        this.props.onSuccess();
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
                <DialogTitle>{!initialValues ? 'Добавление' : 'Редактирование'} класса участия</DialogTitle>
                <DialogContent>
                    <form onSubmit={this.handleSubmit} id={"participation_class-form"}>
                        <Grid container spacing={16}>
                            <Grid item xs={12}>
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
                        </Grid>
                    </form>
                    {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
                </DialogContent>
                <DialogActions>
                    <Grid container spacing={0} justify={`space-between`}>
                        <Grid item>
                            {isUpdate &&
                            <ConfirmDialog
                                onConfirm={() => this.handleDelete(isUpdate)}
                                trigger={<Button
                                    color={"secondary"}
                                    disabled={submitting}
                                >
                                    Удалить
                                </Button>}
                            />
                            }
                        </Grid>
                        <Grid item>
                            <Grid container spacing={8}>
                                <Grid item>
                                    <Button
                                        color={"primary"}
                                        disabled={submitting}
                                        onClick={this.handleCancel}
                                    >
                                        Отмена
                                    </Button>
                                </Grid>
                                <Grid item>
                                    <Button
                                        variant={"contained"}
                                        color={"primary"}
                                        form={"participation_class-form"}
                                        type={"submit"}
                                        disabled={submitting}
                                    >
                                        {!initialValues ? 'Добавить' : 'Сохранить'}
                                    </Button>
                                </Grid>
                            </Grid>
                        </Grid>
                    </Grid>
                </DialogActions>
            </Dialog>
        );
    }
}

ParticipationClassForm.propTypes = {
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

export default ParticipationClassForm;