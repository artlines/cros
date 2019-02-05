import React from "react";
import {connect} from 'react-redux';
import PropTypes from "prop-types";
import {
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    Grid,
    TextField, MenuItem,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';
import API from '../../../libs/api';
import ErrorMessage from "../../utils/ErrorMessage";
import map from "lodash/map";
import CKEditorField from "../../utils/CKEditorField";
import ConfirmDialog from "../../utils/ConfirmDialog";
import WysiwygField from "../../utils/WysiwygField";

const api = new API();

class RoomTypeForm extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            values: {
                title: '',
                max_places: '',
                description: '',
                cost: '',
                participation_class_id: '',
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

    handleChange = (field) => (event) => {
        const { values, errors } = this.state;
        delete(errors[field]);

        const value = event.editor ? event.editor.getData() : event.target.value;

        this.setState({
            values: {
                ...values,
                [field]: value,
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
            ? api.put(`room_type/${values.id}`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit)
            : api.post(`room_type/new`, values)
                .then(this.handleSuccessSubmit)
                .catch(this.handleErrorSubmit);
    };

    handleDelete = (id) => {
        api.delete(`room_type/${id}`)
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit);
    };

    handleSuccessSubmit = () => {
        this.setState({submitting: false});
        this.props.onSuccess();
    };

    handleErrorSubmit = (err) => this.setState({submitting: false, submitError: err.message});

    render() {
        const { initialValues, participation_classes } = this.props;
        const { values, errors, submitting, submitError } = this.state;

        const isUpdate = initialValues && initialValues.id;

        return (
            <React.Fragment>
                <form onSubmit={this.handleSubmit} id={"room_type-form"}>
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
                                InputLabelProps={{
                                    shrink: true,
                                }}
                            />
                        </Grid>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                required
                                label={`Класс участия`}
                                value={values.participation_class_id}
                                margin={"dense"}
                                fullWidth
                                variant={"outlined"}
                                name={`participation_class_id`}
                                onChange={this.handleChange('participation_class_id')}
                                error={!!errors.participation_class_id}
                                helperText={errors.participation_class_id}
                                select={true}
                                InputLabelProps={{
                                    shrink: true,
                                }}
                            >
                                {map(participation_classes, pc =>
                                    <MenuItem key={pc.id} value={pc.id}>{pc.title}</MenuItem>
                                )}
                            </TextField>
                        </Grid>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                required
                                label={"Стоимость"}
                                type={"number"}
                                value={values.cost}
                                margin={"dense"}
                                fullWidth
                                variant={"outlined"}
                                name={'cost'}
                                onChange={this.handleChange('cost')}
                                error={!!errors.cost}
                                helperText={errors.cost}
                                InputLabelProps={{shrink: true}}
                            />
                        </Grid>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                required
                                label={"Количество мест"}
                                type={"number"}
                                value={values.max_places}
                                margin={"dense"}
                                fullWidth
                                variant={"outlined"}
                                name={'max_places'}
                                onChange={this.handleChange('max_places')}
                                error={!!errors.max_places}
                                helperText={errors.max_places}
                                InputLabelProps={{shrink: true}}
                            />
                        </Grid>
                        <Grid item xs={12} style={{textAlign: 'center'}}>
                            <WysiwygField
                                required
                                name={`description`}
                                label={`Описание`}
                                value={values.description}
                                onChange={this.handleChange('description')}
                                error={!!errors.description}
                                helperText={errors.description}
                            />
                        </Grid>
                    </Grid>
                </form>
                {submitError && <ErrorMessage description={submitError} extended={true}/>} {/*title={} description={} extended={}*/}
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
                                    form={"room_type-form"}
                                    type={"submit"}
                                    disabled={submitting}
                                >
                                    {!initialValues ? 'Добавить' : 'Сохранить'}
                                </Button>
                            </Grid>
                        </Grid>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

RoomTypeForm.propTypes = {
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

const mapStateToProps = state =>
    ({
        participation_classes: state.abode.participation_class.items,
    });

export default connect(mapStateToProps)(RoomTypeForm);