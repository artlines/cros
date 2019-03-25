import React from "react";
import PropTypes from "prop-types";
import {
    FormControl,
    FormHelperText,
    InputLabel,
    Input,
} from "@material-ui/core";
//import 'ckeditor/ckeditor';
import CKEditor from 'ckeditor4-react';
import API from "../../libs/api";

const api = new API();

class WysiwygField extends React.PureComponent {
    componentDidMount() {
        const { name } = this.props;

        //CKEDITOR.replace(name, {
            //filebrowserUploadUrl: `${api.getApiHost()}/attachment/upload_public`,
            //stylesSet: [],
        //});
    }

    render() {
        const { name, label, helperText, onChange, value, error, required, fullWidth } = this.props;

        return (
            <React.Fragment>
                <FormControl error={error} required={required} fullWidth={fullWidth}>
                    <InputLabel htmlFor={name} required={required} shrink>{label}</InputLabel>
                    {/*<Input disableUnderline multiline name={name} onChange={onChange} value={value}/>*/}
                    <div style={{ marginTop: 20 }}>
                        <CKEditor
                            data={value}
                            config={{
                                filebrowserUploadUrl: `${api.getApiHost()}/attachment/upload_public`,
                                baseFloatZIndex: 102000,
                            }}
                            onChange={onChange}
                        />
                    </div>
                    <FormHelperText id={`${name}-error-text`}>{helperText || ""}</FormHelperText>
                </FormControl>
            </React.Fragment>
        );
    }
}

WysiwygField.propTypes = {
    name:       PropTypes.string.isRequired,
    label:      PropTypes.string.isRequired,
    onChange:   PropTypes.func.isRequired,

    value:      PropTypes.string,
    helperText: PropTypes.string,
    error:      PropTypes.bool,
    required:   PropTypes.bool,
    fullWidth:  PropTypes.bool,
};

WysiwygField.defaultProps = {
    fullWidth: false,
};

export default WysiwygField;