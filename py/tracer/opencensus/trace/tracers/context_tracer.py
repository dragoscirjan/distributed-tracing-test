import logging

from opencensus.trace import execution_context
from opencensus.trace.tracers.context_tracer import ContextTracer as BaseContextTracer
from opencensus.trace.tracers import base
from tracer.opencensus.trace import span as trace_span

class ContextTracer(BaseContextTracer):
    def start_span(self, name='span'):
        """Start a span.

        :type name: str
        :param name: The name of the span.

        :rtype: :class:`~opencensus.trace.span.Span`
        :returns: The Span object.
        """
        parent_span = self.current_span()

        # If a span has remote parent span, then the parent_span.span_id
        # should be the span_id from the request header.
        if parent_span is None:
            parent_span = base.NullContextManager(
                span_id=self.span_context.span_id)
        logging.debug('>>>>> parent_span: %s' % parent_span.span_id)


        span = trace_span.Span(
            name,
            parent_span=parent_span,
            context_tracer=self)
        with self._spans_list_condition:
            self._spans_list.append(span)
        self.span_context.span_id = span.span_id
        execution_context.set_current_span(span)
        span.start()
        return span