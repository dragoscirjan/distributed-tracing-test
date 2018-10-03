import logging
import re

from opencensus.trace.span_context import SpanContext as BaseSpanContext, generate_trace_id
from opencensus.trace import trace_options

_INVALID_TRACE_ID = '0' * 32
INVALID_SPAN_ID = '0' * 16

TRACE_ID_PATTERN = re.compile('[0-9a-f]{32}?')
SPAN_ID_PATTERN = re.compile('[0-9a-f]{8}?')

# Default options, enable tracing
DEFAULT_OPTIONS = 1

# Default trace options
DEFAULT = trace_options.TraceOptions(DEFAULT_OPTIONS)

class SpanContext(BaseSpanContext):
    def __init__(
            self,
            trace_id=None,
            span_id=None,
            trace_options=None,
            tracestate=None,
            from_header=False):
        if trace_id is None:
            trace_id = generate_trace_id()

        if trace_options is None:
            trace_options = DEFAULT

        self.from_header = from_header
        self.trace_id = self._check_trace_id(trace_id)
        self.span_id = self._check_span_id(span_id)
        self.trace_options = trace_options
        self.tracestate = tracestate

    def _check_span_id(self, span_id):
        """Check the format of the span_id to ensure it is 16-character hex
        value representing a 64-bit number. If span_id is invalid, logs a
        warning message and returns None

        :type span_id: str
        :param span_id: Identifier for the span, unique within a span.

        :rtype: str
        :returns: Span_id for the current span.
        """
        if span_id is None:
            return None
        assert isinstance(span_id, str)

        if span_id is INVALID_SPAN_ID:
            logging.warning(
                'Span_id {} is invalid (cannot be all zero)'.format(span_id))
            self.from_header = False
            return None

        match = SPAN_ID_PATTERN.match(span_id)

        if match:
            return span_id
        else:
            logging.warning(
                'Span_id {} does not the match the '
                'required format'.format(span_id))
            self.from_header = False
            return None