import React from "react";
import PropTypes from "prop-types";
import {
    FormControl,
    FormHelperText,
    InputLabel,
} from "@material-ui/core";
import CKEditor from 'ckeditor4-react';
import API from "../../libs/api";

const api = new API();

class WysiwygField extends React.PureComponent {

    render() {
        const { name, label, helperText, onChange, value, error, required, fullWidth, height } = this.props;
        let config = {
            filebrowserUploadUrl: `${api.getApiHost()}/attachment/upload_public`,
            baseFloatZIndex: 102000,
            allowedContent: true,
        };

        height && (config.height = height);

        return (
            <React.Fragment>
                <FormControl error={error} required={required} fullWidth={fullWidth}>
                    <InputLabel htmlFor={name} required={required} shrink>{label}</InputLabel>
                    <div style={{ marginTop: 20 }}>
                        <CKEditor
                            data={value}
                            config={config}
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

    height:     PropTypes.number,
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