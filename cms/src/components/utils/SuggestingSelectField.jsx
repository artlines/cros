import React from "react";
import Select from "react-select";
import {
    Typography,
    TextField,
    MenuItem,
    Chip,
    Paper, FormControl, InputLabel, Grid,
} from "@material-ui/core";
import {
    Cancel as CancelIcon,
} from "@material-ui/icons";
import { withStyles } from "@material-ui/core/styles";
import { emphasize } from "@material-ui/core/styles/colorManipulator";
import classNames from "classnames";
import find from 'lodash/find';

const styles = theme => ({
    root: {
        flexGrow: 1,
        height: 250,
    },
    input: {
        display: "flex",
        padding: 0,
        marginTop: 12,
    },
    valueContainer: {
        display: "flex",
        flexWrap: "wrap",
        flex: 1,
        alignItems: "center",
        overflow: "hidden",
    },
    chip: {
        margin: `5px 2px`,
        height: 26,
    },
    chipFocused: {
        backgroundColor: emphasize(
            theme.palette.type === "light" ? theme.palette.grey[300] : theme.palette.grey[700],
            0.08,
        ),
    },
    noOptionsMessage: {
        padding: `${theme.spacing.unit}px ${theme.spacing.unit * 2}px`,
    },
    singleValue: {
        fontSize: 16,
    },
    placeholder: {
        position: "absolute",
        left: 2,
        fontSize: 16,
    },
    paper: {
        position: "absolute",
        zIndex: 1400,
        marginTop: theme.spacing.unit,
        left: 0,
        right: 0,
    },
    divider: {
        height: theme.spacing.unit * 2,
    },
});

function NoOptionsMessage(props) {
    return (
        <Typography
            color="textSecondary"
            className={props.selectProps.classes.noOptionsMessage}
            {...props.innerProps}
        >
            {props.children}
        </Typography>
    );
}

function inputComponent({ inputRef, ...props }) {
    return <div ref={inputRef} {...props} />;
}

function Control(props) {
    return (
        <TextField
            fullWidth
            InputProps={{
                inputComponent,
                inputProps: {
                    className: props.selectProps.classes.input,
                    inputRef: props.innerRef,
                    children: props.children,
                    ...props.innerProps,
                },
            }}
            {...props.selectProps.textFieldProps}
        />
    );
}

function Option(props) {
    return (
        <MenuItem
            buttonRef={props.innerRef}
            selected={props.isFocused}
            component="div"
            style={{
                fontWeight: props.isSelected ? 500 : 400,
            }}
            {...props.innerProps}
        >
            {props.children}
        </MenuItem>
    );
}

function Placeholder(props) {
    return (
        <Typography
            color="textSecondary"
            className={props.selectProps.classes.placeholder}
            {...props.innerProps}
        >
            {props.children}
        </Typography>
    );
}

function SingleValue(props) {
    const { options, data: value } = props;

    const data = find(options, {value});

    return (
        <Typography className={props.selectProps.classes.singleValue} {...props.innerProps}>
            {data.label}
        </Typography>
    );
}

function ValueContainer(props) {
    return <div className={props.selectProps.classes.valueContainer}>{props.children}</div>;
}

function MultiValue(props) {
    return (
        <Chip
            tabIndex={-1}
            label={props.children}
            className={classNames(props.selectProps.classes.chip, {
                [props.selectProps.classes.chipFocused]: props.isFocused,
            })}
            onDelete={props.removeProps.onClick}
            deleteIcon={<CancelIcon {...props.removeProps} />}
        />
    );
}

function Menu(props) {
    return (
        <Paper square className={props.selectProps.classes.paper} {...props.innerProps}>
            {props.children}
        </Paper>
    );
}

const components = {
    Control,
    Menu,
    MultiValue,
    NoOptionsMessage,
    Option,
    Placeholder,
    SingleValue,
    ValueContainer,
};

class SuggestingSelectField extends React.PureComponent {
    render() {
        const { classes, theme, fullWidth, required, label, ...props } = this.props;

        const selectStyles = {
            input: base => ({
                ...base,
                color: theme.palette.text.primary,
                "& input": {
                    font: "inherit",
                },
            }),
        };

        return (
            <FormControl fullWidth={fullWidth} required={required}>
                <InputLabel shrink>{label}</InputLabel>
                <Select
                    style={{marginTop: 20}}
                    classes={classes}
                    styles={selectStyles}
                    components={components}
                    noOptionsMessage={() => `Нет результатов`}
                    {...props}
                />
            </FormControl>
        );
    }
}

export default withStyles(styles, {withTheme: true})(SuggestingSelectField);