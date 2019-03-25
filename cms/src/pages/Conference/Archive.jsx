import React from 'react';
import {connect} from 'react-redux';
import participating from '../../actions/participating';
import isEqual from 'lodash/isEqual';
import WysiwygField from "../../components/utils/WysiwygField";
import ErrorMessage from "../../components/utils/ErrorMessage";
import {
    Grid,
    Button,
} from '@material-ui/core';
import API from '../../libs/api';

const api = new API();

class Archive extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            content: '',
            submitting: false,
            submitError: false,
        };
    }

    componentDidMount() {
        this.props.fetchArchive();
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { item } = this.props.conference;

        if (!isEqual(prevProps.conference.item, item)) {
            this.setState({content: item.content});
        }
    }

    handleChange = event => {
        const content = event.editor.getData();
        this.setState({content, submitError: false});
    };

    handleSubmit = () => {
        const {content} = this.state;
        const id = Number(this.props.match.params.id);

        this.setState({
            submitting: true,
            submitError: false,
        });

        api.put(`conference/${id}/archive`, {content})
            .then(this.handleSuccessSubmit)
            .catch(this.handleErrorSubmit);
    };

    handleSuccessSubmit = () => this.setState({submitting: false});

    handleErrorSubmit = (err) => this.setState({submitting: false, submitError: err.message});

    render() {
        const { content, submitting, submitError } = this.state;

        return (
            <Grid container spacing={16} style={{textAlign: 'center'}}>
                <Grid xs={12}>
                    <WysiwygField fullWidth name={`content`} label={`Архив`} onChange={this.handleChange} value={content}/>
                </Grid>
                <Grid xs={12}>
                    {submitError && <ErrorMessage description={submitError} extended={true}/>}
                </Grid>
                <Grid xs={12}>
                    <Button disabled={submitting} size={`large`} variant={`contained`} color={`primary`} onClick={this.handleSubmit}>Сохранить</Button>
                </Grid>
            </Grid>
        );
    }
}

const mapStateToProps = state =>
    ({
        conference: state.participating.conference,
    });

const mapDispatchToProps = (dispatch, ownProps) =>
    ({
        fetchArchive: () => {
            const id = Number(ownProps.match.params.id);
            dispatch(participating.fetchConferenceArchive(id));
        },
    });

export default connect(mapStateToProps, mapDispatchToProps)(Archive);