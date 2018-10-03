import logging
import random
import uuid

from opencensus.trace.span import Span as BaseSpan, SpanKind

def new_generate_span_id():
    """Return the random generated span ID for a span. Must be a 16 character
    hexadecimal encoded string
    :rtype: str
    :returns: 16 digit randomly generated hex trace id.
    """
    logging.error('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>')
    # span_id = "%x" % random.randint(1, 255)
    # return span_id
    span_id = uuid.uuid4().hex[:8]
    return span_id

class Span(BaseSpan):
    def __init__(
            self,
            name,
            parent_span=None,
            attributes=None,
            start_time=None,
            end_time=None,
            span_id=None,
            stack_trace=None,
            time_events=None,
            links=None,
            status=None,
            same_process_as_parent_span=None,
            context_tracer=None,
            span_kind=SpanKind.UNSPECIFIED):
        self.name = name
        self.parent_span = parent_span
        self.start_time = start_time
        self.end_time = end_time

        if span_id is None:
            span_id = new_generate_span_id()

        if attributes is None:
            attributes = {}

        # Do not manipulate spans directly using the methods in Span Class,
        # make sure to use the Tracer.
        if parent_span is None:
            parent_span = base.NullContextManager()

        if time_events is None:
            time_events = []

        if links is None:
            links = []

        self.attributes = attributes
        self.span_id = span_id
        self.stack_trace = stack_trace
        self.time_events = time_events
        self.links = links
        self.status = status
        self.same_process_as_parent_span = same_process_as_parent_span
        self._child_spans = []
        self.context_tracer = context_tracer
        self.span_kind = span_kind
        for callback in Span._on_create_callbacks:
            callback(self)

    _on_create_callbacks = []